<?php
class InnerMail extends CApplicationComponent {
	public $connectionID='db';
	public $db;
	public $serverCount = 3;
	public $headerTable = "{{inner_mail_header_%d}}";
	public $messageTable = "{{inner_mail_message_%d}}";
	public $folderTable = "{{inner_mail_folder_%d}}";
	public $blockTable = "{{inner_mail_block}}";
	public $scamTable = "{{inner_mail_scam}}";
	public $headersPageSize = 20;
	public $messagesPageSize = 30;
	public $blockedUsersPageSize=15;
	public $expiredMessageTime=604800; // 30 days

	public $events = array(
		'onNewMessageCreated' => array(),
		'onAddToChain' => array(),
		'onSenderBlocked' => array(),
		'onSenderUnBlocked' => array(),
		'onSenderMarkedAsScammer'=>array(),
	);
	public function init() {
		$this->setDb();
		$this->attachEvents();
	}

	public function box(User $user) {
		return UserInnerMailBox::get($user, $this);
	}

	protected function attachEvents() {
		foreach($this->events as $event => $handlers) {
			if(method_exists($this, $event)) {
				foreach($handlers as $handler) {
					$this->attachEventHandler($event, $handler);
				}
			}
		}
	}

	/**
	* @return set the DB Component
	* @throws CException if {@link connectionID} does not point to a valid application component.
	*/
	protected function setDb() {
		if(!(($this->db=Yii::app()->getComponent($this->connectionID)) instanceof CDbConnection)) {
			throw new CException(Yii::t('yii','InnerMail.connectionID "{id}" is invalid. Please make sure it refers to the ID of a CDbConnection application component.',
				array('{id}'=>$this->connectionID)));
		}
	}

	public function onNewMessageCreated (CEvent $event) {
		$this->raiseEvent('onNewMessageCreated', $event);
	}

	public function onAddToChain (CEvent $event) {
		$this->raiseEvent('onAddToChain', $event);
	}

	public function onSenderBlocked(CEvent $event) {
		$this->raiseEvent('onSenderBlocked', $event);
	}

	public function onSenderUnBlocked (CEvent $event) {
		$this->raiseEvent('onSenderUnBlocked', $event);
	}

	public function onSenderMarkedAsScammer (CEvent $event) {
		$this->raiseEvent('onSenderMarkedAsScammer', $event);
	}

	public function getHeaderTable($server_id) {
		return sprintf($this->headerTable, $server_id);
	}

	public function getFolderTable($server_id) {
		return sprintf($this->folderTable, $server_id);
	}

	public function getMessageTable($server_id) {
		return sprintf($this->messageTable, $server_id);
	}

	public function removeExpiredMessages() {
		$expired_time=date("Y-m-d H:i:s", time()-(60*60*24*7)); // 7 days ago
		for($i=0; $i<$this->serverCount; $i++) {
			$headerTable=$this->getHeaderTable($i);
			$folderTable=$this->getFolderTable($i);
			$messageTable=$this->getMessageTable($i);
			$command=$this->db->createCommand();
			$expired=$command
				->select("header_id, owner_id")
				->from($folderTable)
				->where("type=:type AND state=:state AND appeared_date<:expired_time", array(
					":type"=>UserInnerMailBox::FOLDER_TRASH,
					":state"=>UserInnerMailBox::FOLDER_STATE_VISIBLE,
					":expired_time"=>$expired_time,
				))
				->queryAll();
				$command->reset();
			foreach($expired as $exp) {
				$transaction=$this->db->beginTransaction();
				try {
					$command->delete($folderTable, array(
						"owner_id"=>$exp['owner_id'],
						"header_id"=>$exp['header_id'],
					));
					$command->reset();

					$command->delete($headerTable, array(
						"id"=>$exp['header_id'],
						"owner_id"=>$exp['owner_id'],
					));
					$command->reset();

					$command->delete($messageTable, array(
						"owner_id"=>$exp['owner_id'],
						"header_id"=>$exp['header_id'],
					));
					$command->reset();

					$transaction->commit();
				} catch(Exception $e) {
					Yii::log($e->getMessage(), 'error', 'application.innermail.expired_messages');
					$transaction->rollback();
					return false;
				}
			}
		}
		return true;
	}
}

class UserInnerMailBox {
	private static $boxes = array();

	public $owner, $db, $innerMail, $headerTable, $messageTable, $folderTable, $date;
	public $blockStack=array();
	protected $newMessageCount=null;
	public $folderStack=array();

	const FOLDER_STATE_VISIBLE=1;
	const FOLDER_STATE_INVISIBLE=0;

	const FOLDER_INBOX = 0;
	const FOLDER_SENT = 1;
	const FOLDER_TRASH = 2;
	const FOLDER_STARRED = 3;
	const FOLDER_IMPORTANT = 4;

	const TYPE_TEXT = 0;

	const STATUS_NEW = 0;
	const STATUS_READ = 1;

	private function __construct($user, $innerMail) {
		$this->owner = $user;
		$this->db = $innerMail->db;
		$this->innerMail = $innerMail;
		$this->headerTable = $this->innerMail->getHeaderTable($this->owner->post_server_id);
		$this->messageTable = $this->innerMail->getMessageTable($this->owner->post_server_id);
		$this->folderTable = $this->innerMail->getFolderTable($this->owner->post_server_id);
		$this->date=date("Y-m-d H:i:s");
	}

	public static function get(User $user, InnerMail $innerMail) {
		if(isset(self::$boxes[$user->id])) {
			return self::$boxes[$user->id];
		}
		return self::$boxes[$user->id] = new self($user, $innerMail);
	}

	public function generateUserBoxID() {
		return $this->owner->id % $this->innerMail->serverCount;
	}

	public function isNew($status) {
		return self::STATUS_NEW==$status;
	}

	public function isInInboxFolder($header_id, $state=UserInnerMailBox::FOLDER_STATE_VISIBLE) {
		return in_array($header_id, $this->getHeadersInFolder(self::FOLDER_INBOX, $state));
	}
	public function isInSentFolder($header_id, $state=UserInnerMailBox::FOLDER_STATE_VISIBLE) {
		return in_array($header_id, $this->getHeadersInFolder(self::FOLDER_SENT, $state));
	}
	public function isInTrashFolder($header_id, $state=UserInnerMailBox::FOLDER_STATE_VISIBLE) {
		return in_array($header_id, $this->getHeadersInFolder(self::FOLDER_TRASH, $state));
	}
	public function isInStarredFolder($header_id, $state=UserInnerMailBox::FOLDER_STATE_VISIBLE) {
		return in_array($header_id, $this->getHeadersInFolder(self::FOLDER_STARRED, $state));
	}
	public function isInImportantFolder($header_id, $state=UserInnerMailBox::FOLDER_STATE_VISIBLE) {
		return in_array($header_id, $this->getHeadersInFolder(self::FOLDER_IMPORTANT, $state));
	}

	public function getHeadersInFolder($folder, $state) {
		if(isset($this->folderStack[$folder."_".$state])) {
			return $this->folderStack[$folder."_".$state];
		}
		$headers=array();
		$dataReader=$this->folderStack[$folder."_".$state]=$this->db->createCommand()
			-> select('header_id')
			-> from($this->folderTable)
			-> where("owner_id=:owner_id AND type=:type AND state=:state", array(
				":owner_id"=>$this->owner->id,
				":type"=>$folder,
				":state"=>$state,
			))
			->query();
		foreach($dataReader as $row) {
			$headers[]=$row['header_id'];
		}
		return $this->folderStack[$folder."_".$state]=$headers;
	}

	public function isOwnerMessage(array $message) {
		return $message['from']==$this->owner->id;
	}

	public function getUpcomingHeaders($cnt, &$senders, $folder=UserInnerMailBox::FOLDER_INBOX) {
		$senders=$headers=array();
		$dataReader = $this->db->createCommand()
			->select("h.*")
			->from("{$this->headerTable} as h")
			->leftJoin("{$this->folderTable} as f", 'h.id=f.header_id AND f.owner_id=:owner_id AND f.type=:type AND f.state=:state')
			->where("h.owner_id=:owner_id AND h.status=:status", array(
				":owner_id"=>$this->owner->id,
				":type"=>self::FOLDER_INBOX,
				":state"=>self::FOLDER_STATE_VISIBLE,
				":status"=>self::STATUS_NEW,
			))
			->order("h.sent_date DESC")
			->limit($cnt)
			->query();
		foreach($dataReader as $row) {
			$senders[]=$row['companion_id'];
			$headers[]=$row;
		}
		return $headers;
	}

	public function getHeaders(&$pgNr, &$pgCnt, &$total, $folderType=UserInnerMailBox::FOLDER_INBOX) {
		$command = $this->db->createCommand();
		$total = $command
			-> select('count(*) as cnt')
			-> from("{$this->folderTable} as f")
			-> where("f.owner_id=:owner_id AND f.type=:type AND f.state=:state", array(
				':owner_id'=>$this->owner->id,
				':type'=>$folderType,
				':state'=>self::FOLDER_STATE_VISIBLE,
			))
			-> queryScalar();

		if($total == 0) {
			$pgCnt = 0;
			return array();
		}
		$command->reset();
		$pgCnt = ceil($total / $this->innerMail->headersPageSize);
		$pgNr = abs((int)$pgNr);
		if ($pgNr <= 0) $pgNr = 1;
		if ($pgCnt < $pgNr) $pgNr = $pgCnt;
		$offset = ($pgNr - 1) * $this->innerMail->headersPageSize;
		return $rows = $command
			-> select('f.appeared_date, h.*')
			-> from("{$this->folderTable} as f")
			-> leftJoin("{$this->headerTable} as h","h.id = f.header_id")
			-> where("f.owner_id=:owner_id AND f.type=:type AND f.state=:state", array(
				':owner_id'=>$this->owner->id,
				':type'=>$folderType,
				':state'=>self::FOLDER_STATE_VISIBLE,
			))
			-> order("FIELD (h.status, ".self::STATUS_NEW.") DESC, f.appeared_date DESC")
			-> limit($this->innerMail->headersPageSize, $offset)
			-> queryAll();
	}

	public function getHeader($id) {
		return $this->db->createCommand()
			-> select('*')
			-> from($this->headerTable)
			-> where('id=:id AND owner_id=:owner_id', array(':id'=>$id, ':owner_id'=>$this->owner->id))
			-> queryRow();
	}

	public function getHeaderByChainID($chain_id) {
		return $this->db->createCommand()
			-> select('*')
			-> from($this->headerTable)
			-> where('owner_id=:owner_id AND chain_id=:chain_id', array(
				':owner_id'=>$this->owner->id,
				':chain_id'=>$chain_id,
			))
			-> queryRow();
	}

	public function removeHeaders(array $headers) {
		return $this->db->createCommand()->delete($this->headerTable,
			array('and', 'owner_id=:owner_id', array('in', 'id', $headers)),
			array(':owner_id'=>$this->owner->id)
		);
	}

	public function getMessages($header_id, $pgNr, & $pgCnt, & $total, $order="DESC") {
		$order=strtoupper($order)=="DESC" ? "DESC" : "ASC";
		$command = $this->db->createCommand();
		$total = $command
			-> select('count(*) as cnt')
			-> from($this->messageTable)
			-> where('owner_id=:owner_id AND header_id=:header_id', array(
				':owner_id'=>$this->owner->id,
				':header_id'=>$header_id,
			))
			-> queryScalar();
		if($total == 0) {
			$pgCnt = 0;
			return array();
		}

		$command->reset();
		$pgCnt = ceil ($total / $this->innerMail->messagesPageSize);
		$pgNr = abs ((int)$pgNr);
		if ($pgNr > 0) $pgNr --;
		if ($pgCnt < $pgNr) return array ();
		$offset = $pgNr * $this->innerMail->messagesPageSize;

		$list = $command
			-> select('*')
			-> from($this->messageTable)
			-> where('owner_id=:owner_id AND header_id=:header_id', array(
				':owner_id'=>$this->owner->id,
				':header_id'=>$header_id
			))
			->order("sent_date $order")
			->limit($this->innerMail->messagesPageSize, $offset)
			->queryAll();
		return $list; // ? array_reverse ($list) : array (); // Only if new messages displayed at the bottom of the container
	}

	private function updateHeaders($id, $data) {
		$headers=is_array($id) ? $id : array($id);
		return $this->db->createCommand()->update($this->headerTable, $data, array('and', 'owner_id=:owner_id', array('in', 'id', $headers)), array(
			':owner_id'=>$this->owner->id,
		));
	}

	public function markAsRead($id) {
		return $this->updateHeaders($id, array(
			'status'=>self::STATUS_READ,
		));
	}

	public function markAsImportant(array $headers=array()) {
		return $this->batchInsertIntoFolders($headers, array(
			self::FOLDER_IMPORTANT,
		));
	}

	public function markAsStarred(array $headers=array()) {
		return $this->batchInsertIntoFolders($headers, array(
			self::FOLDER_STARRED,
		));
	}

	public function removeMessages(array $headers) {
		return $this->db->createCommand()->delete($this->messageTable,
			array("and", "owner_id=:owner_id", array('in', 'header_id', $headers)),
			array(":owner_id"=>$this->owner->id)
		);
	}

	public function insertMessage($header_id, $sender_id, $message, $type) {
		return $this->db->createCommand()->insert($this->messageTable, array(
			"owner_id"=>$this->owner->id,
			"header_id"=>$header_id,
			"from"=>$sender_id,
			"message"=>$message,
			"type"=>$type,
			"sent_date"=>$this->date,
		));
	}

	public function moveToTrashFolder(array $headers) {
		if($this->db->getCurrentTransaction()===null)
			$transaction=$this->db->beginTransaction();
		try {
			$updFields=array(
				"state"=>self::FOLDER_STATE_INVISIBLE,
			);
			$affected = $this->db->createCommand()->update($this->folderTable, $updFields,
				array('and', 'owner_id=:owner_id AND type!=:type AND state!=:state', array('in', 'header_id', $headers)), array(
				":type"=>self::FOLDER_TRASH,
				":owner_id"=>$this->owner->id,
				":state"=>self::FOLDER_STATE_INVISIBLE,
			));
			$this->markAsRead($headers);
			$this->batchInsertIntoFolders($headers, array(
				self::FOLDER_TRASH,
			));
			if(isset($transaction))
				$transaction->commit();
			return true;
		} catch(Exception $e) {
			if(isset($transaction)) {
				$transaction->rollback();
				Yii::log($e->getMessage(), 'error', 'application.innermail.move_to_trash_folder');
				return false;
			}
			throw $e;
		}
	}

	public function restoreFromTrash(array $headers) {
		if($this->db->getCurrentTransaction()===null)
			$transaction=$this->db->beginTransaction();
		try {
			$this->batchRemoveFolders($headers, array(
				self::FOLDER_TRASH,
			));
			$this->updateFolderState($headers, self::FOLDER_STATE_VISIBLE);
			if(isset($transaction))
				$transaction->commit();
			return true;
		} catch(Exception $e) {
			if(isset($transaction)) {
				$transaction->rollback();
				Yii::log($e->getMessage(), 'error', 'application.innermail.restore_from_trash');
				return false;
			}
			throw $e;
		}
	}

	public function completelyRemove(array $headers) {
		if($this->db->getCurrentTransaction()===null)
			$transaction=$this->db->beginTransaction();
		try {
			$this->batchRemoveFolders($headers, $this->getAllFolderID());
			$this->removeMessages($headers);
			$this->removeHeaders($headers);
			if(isset($transaction))
				$transaction->commit();
			return true;
		} catch(Exception $e) {
			if(isset($transaction)) {
				$transaction->rollback();
				Yii::log($e->getMessage(), 'error', 'application.innermail.completely_remove');
				return false;
			}
			throw $e;
		}
	}

	public function batchRemoveFolders(array $headers, array $folders) {
		if(empty($headers) OR empty($folders)) {
			return false;
		}
		return $this->db->createCommand()->delete($this->folderTable,
			array('and', 'owner_id=:owner_id', array('in', 'header_id', $headers), array('in', 'type', $folders)),
			array(':owner_id'=>$this->owner->id)
		);
	}

	public function removeFromStarred(array $headers=array()) {
		return $this->batchRemoveFolders($headers, array(self::FOLDER_STARRED));
	}

	public function removeFromImportant(array $headers=array()) {
		return $this->batchRemoveFolders($headers, array(self::FOLDER_IMPORTANT));
	}

	public function getAllFolderID() {
		return array_keys($this->namedFolders());
	}

	public function namedFolders() {
		return array(
			self::FOLDER_INBOX=>"inbox",
			self::FOLDER_IMPORTANT=>"important",
			self::FOLDER_SENT=>"sent",
			self::FOLDER_STARRED=>"starred",
			self::FOLDER_TRASH=>"trash",
		);
	}

	public function getNameFolderByID($folder_id) {
		$folders=$this->namedFolders();
		return isset($folders[$folder_id]) ? $folders[$folder_id] : $folders[self::FOLDER_INBOX];
	}

	public function getValidFolderID($folder_id) {
		$folders=$this->namedFolders();
		return isset($folders[$folder_id]) ? $folder_id : self::FOLDER_INBOX;
	}

	public function updateFolderState($headers, $state=self::FOLDER_STATE_VISIBLE) {
		$updFields=array(
			'state'=>$state,
		);
		return $this->db->createCommand()->update($this->folderTable, $updFields,
			array('and', 'owner_id=:owner_id', array('in', 'header_id', $headers)),
			array(':owner_id'=>$this->owner->id)
		);
	}

	public function batchInsertIntoFolders(array $headers, array $folders) {
		if(empty($headers) OR empty($folders)) {
			return false;
		}
		$batchData=array();
		foreach($headers as $header) {
			foreach($folders as $folder) {
				$batchData[]=array(
					'owner_id'=>$this->owner->id,
					'header_id'=>(int) $header,
					'type'=>(int) $folder,
					'state'=>self::FOLDER_STATE_VISIBLE,
					'appeared_date'=>$this->date,
				);
			}
		}
		//$sql="INSERT IGNORE INTO {$this->folderTable} (owner_id, header_id, type, state, appeared_date) VALUES ";
		$sql="INSERT INTO {$this->folderTable} (owner_id, header_id, type, state, appeared_date) VALUES ";

/*INSERT INTO sc_post_folder_0 (owner_id, header_id, type, appeared_date, state) VALUES (1, 15, 0, '2014-01-01 01:01:01', 1)
ON DUPLICATE KEY UPDATE appeared_date='2014-01-01 01:01:01'*/

		$params=array();
		foreach($batchData as $id=>$data) {
			$values="";
			foreach($data as $field=>$value) {
				$values.=":{$field}_{$id}, ";
				$params[":{$field}_{$id}"]=$value;
			}
			$values=substr($values,0,-2);
			$sql.="(".$values."), ";
		}
		$sql=substr($sql,0,-2);
		$sql.=" ON DUPLICATE KEY UPDATE appeared_date=:appeared_date";
		$command=$this->db->createCommand($sql);
		foreach($params as $name=>$value) {
			$command->bindValue($name, $value);
		}
		$command->bindValue(":appeared_date", $this->date, PDO::PARAM_STR);
		return $command->execute();
	}

	/*
	* $typeExpr['all'] == All folders
	* $typeExpr['except'] == Except following folders
	* $typeExpr['in'] == In following folders
	*/
	protected function updateFolderAppearedTime($header_id, array $typeExpr) {
		$folders=$this->getAllFolderID();
		if(isset($typeExpr['except'])) {
			$result=array_diff($folders, $typeExpr['except']);
		} else if(isset($typeExpr['in'])) {
			$result=$typeExpr['in'];
		} else if(isset($typeExpr['all'])) {
			$result=$folders;
		} else {
			return true;
		}
		$updFields=array(
			"appeared_date"=>$this->date,
		);
		return $this->db->createCommand()->update($this->folderTable, $updFields,
			array('and', 'owner_id=:owner_id AND header_id=:header_id', array('in', 'type', $result)),
			array(':owner_id'=>$this->owner->id, ':header_id'=>$header_id)
		);
	}

	public function addToChain($owner_header, User $receiver, $message, $type = UserInnerMailBox::TYPE_TEXT) {
		$receiverBox = $this->innerMail->box($receiver);
		$chain_id = $owner_header['chain_id'];
		$header_id = $owner_header['id'];

		if($this->db->getCurrentTransaction()===null)
			$transaction=$this->db->beginTransaction();
		try {
			$receiverHeader = $receiverBox->getHeaderByChainID($chain_id);
			if(!$receiverHeader) {
				$receiverHeaderID = $receiverBox->insertHeader($this->owner->id, $chain_id, self::STATUS_NEW, $owner_header['subject']);
				$receiverBox->batchInsertIntoFolders(array($receiverHeaderID), array(
					self::FOLDER_INBOX, self::FOLDER_IMPORTANT
				));
			} else {
				$receiverBox->updateHeaders($receiverHeader['id'], array(
					'status' => self::STATUS_NEW,
				));
				$receiverHeaderID=$receiverHeader['id'];
				$receiverBox->restoreFromTrash(array($receiverHeaderID));
				$receiverBox->updateFolderAppearedTime($receiverHeaderID, array(
					'except'=>array(self::FOLDER_TRASH),
				));
			}

			$this->batchInsertIntoFolders(array($header_id), array(self::FOLDER_SENT));

			/*$this->updateFolderAppearedTime($header_id, array(
				'in'=>array(self::FOLDER_SENT),
			));*/

			$receiverBox -> insertMessage($receiverHeaderID, $this->owner->id, $message, $type);
			$this -> insertMessage($header_id, $this->owner->id, $message, $type);

			$this->innerMail->onAddToChain(new CEvent($this, array(
				"receiver"=>$receiver,
				"receiver_box"=>$receiverBox,
				"receiverHeader"=>$receiverHeader,
				"owner_header"=>$owner_header,
				"message"=>$message,
				"type"=>$type,
				"receiver_header_id"=>$receiverHeaderID,
				"chain_id"=>$chain_id,
				"date" => $this->date,
			)));
			if(isset($transaction))
				$transaction->commit();
			return true;
		} catch (Exception $e) {
			if(isset($transaction)) {
				$transaction->rollback();
				Yii::log($e->getMessage(), 'error', 'application.innermail.add_to_chain');
				return false;
			}
			throw $e;
		}
	}

	public function insertHeader($companion_id, $chain_id, $status, $subject) {
		$hr = $this->db->createCommand()->insert($this->headerTable, array(
			"owner_id" => $this->owner->id,
			"companion_id" => $companion_id,
			"chain_id" => $chain_id,
			"status" => $status,
			"subject" => $subject,
			"sent_date" => $this->date,
		));
		return $this->db->lastInsertID;
	}

	public function createNewMessage(User $receiver, $subject, $message, $type = UserInnerMailBox::TYPE_TEXT) {
		// Get user mail box
		$receiverBox = $this->innerMail->box($receiver);
		if($this->db->getCurrentTransaction()===null)
			$transaction=$this->db->beginTransaction();
		try {
			$chain_id = md5(microtime () . $receiver->id . $this->owner->id . mt_rand (0, 100000));

			$receiverHeaderID = $receiverBox->insertHeader($this->owner->id, $chain_id, self::STATUS_NEW, $subject);
			$ownerHeaderID = $this->insertHeader($receiver->id, $chain_id, self::STATUS_READ, $subject);

			$receiverBox->insertMessage($receiverHeaderID, $this->owner->id, $message, $type);
			$this->insertMessage($ownerHeaderID, $this->owner->id, $message, $type);

			$this->batchInsertIntoFolders(array($ownerHeaderID), array(
				self::FOLDER_INBOX, self::FOLDER_SENT, self::FOLDER_IMPORTANT
			));

			$receiverBox->batchInsertIntoFolders(array($receiverHeaderID), array(
				self::FOLDER_INBOX, self::FOLDER_IMPORTANT
			));

			$this->innerMail->onNewMessageCreated(new CEvent($this, array(
				"receiver"=>$receiver,
				"receiver_box"=>$receiverBox,
				"subject"=>$subject,
				"message"=>$message,
				"type"=>$type,
				"chain_id"=>$chain_id,
				"receiver_header_id"=>$receiverHeaderID,
				"date" => $this->date,
			)));
			if(isset($transaction))
				$transaction->commit();
			return true;
		} catch (Exception $e) {
			if(isset($transaction)) {
				$transaction->rollback();
				Yii::log($e->getMessage(), 'error', 'application.innermail.new_message');
				return false;
			}
			throw $e;
		}
	}

	public function getNewMessageCount() {
		if($this->newMessageCount === null) {
			$this->newMessageCount = $this->db->createCommand()
			->select('count(*)')
			->from($this->headerTable)
			->where("owner_id=:owner_id AND status=:status", array(
				":owner_id"=>$this->owner->id,
				":status"=>self::STATUS_NEW,
			))->queryScalar();
		}
		return $this->newMessageCount;
	}

	public function insertIntoBlockTable($block_id) {
		if($block_id==$this->owner->id) { return false; }
		$sql = "INSERT IGNORE INTO {$this->innerMail->blockTable} (user_id, block_id, block_date) VALUES (:user_id, :block_id, :block_date)";
		$command = $this->db->createCommand($sql);
		$user_id = $this->owner->id;
		$command->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$command->bindParam(":block_id", $block_id, PDO::PARAM_INT);
		$command->bindParam(":block_date", $this->date, PDO::PARAM_STR);
		return $command->execute();
	}

	public function getHeadersSentBy($sender_id) {
		$headers=array();
		$dataReader = $this->db->createCommand()
			->select('id')
			->from($this->headerTable)
			->where('owner_id=:owner_id AND companion_id=:companion_id', array(
				':owner_id'=>$this->owner->id,
				':companion_id'=>$sender_id,
			))->query();
		foreach($dataReader as $row) {
			$headers[]=$row['id'];
		}
		return $headers;
	}

	public function blockSender(User $sender) {
		if($sender->id==$this->owner->id) { return false; }
		if($this->db->getCurrentTransaction()===null)
			$transaction=$this->db->beginTransaction();
		try {
			$headers=$this->getHeadersSentBy($sender->id);
			$this->moveToTrashFolder($headers);
			$this->insertIntoBlockTable($sender->id);
			$this->innerMail->onSenderBlocked(new CEvent($this, array(
				"blocked_user"=>$sender,
				"headers"=>$headers,
			)));
			if(isset($transaction))
				$transaction->commit();
			return true;
		} catch (Exception $e) {
			if(isset($transaction)) {
				$transaction->rollback();
				Yii::log($e->getMessage(), 'error', 'application.innermail.block_sender');
				return false;
			}
			throw $e;
		}
	}

	public function unblockSender(User $sender) {
		if($sender->id==$this->owner->id) { return false; }
		if($this->db->getCurrentTransaction()===null)
			$transaction=$this->db->beginTransaction();
		try {
			$this->removeFromBlockTable($sender->id);
			$this->removeFromScamTable($sender->id);
			$headers=$this->getHeadersSentBy($sender->id);
			$this->restoreFromTrash($headers);
			$this->innerMail->onSenderUnBlocked(new CEvent($this, array(
				"blocked_user"=>$sender,
				"headers"=>$headers,
			)));
			if(isset($transaction))
				$transaction->commit();
			return true;
		} catch (Exception $e) {
			if(isset($transaction)) {
				$transaction->rollback();
				Yii::log($e->getMessage(), 'error', 'application.innermail.unblock_sender');
				return false;
			}
			throw $e;
		}
	}

	public function reportScam(User $sender, $chain_id) {
		if($sender->id==$this->owner->id) { return false; }
		if($this->db->getCurrentTransaction()===null)
			$transaction=$this->db->beginTransaction();
		try {
			$headers=$this->getHeadersSentBy($sender->id);
			$this->moveToTrashFolder($headers);
			$this->insertIntoBlockTable($sender->id);
			$this->inserIntoScamTable($sender->id, $chain_id);
			$this->innerMail->onSenderMarkedAsScammer(new CEvent($this, array(
				"blocked_user"=>$sender,
				"headers"=>$headers,
			)));
			if(isset($transaction))
				$transaction->commit();
			return true;
		} catch (Exception $e) {
			if(isset($transaction)) {
				$transaction->rollback();
				Yii::log($e->getMessage(), 'error', 'application.innermail.report_scammer');
				return false;
			}
			throw $e;
		}
	}

	public function removeFromBlockTable($user_id) {
		return $this->db->createCommand()->delete($this->innerMail->blockTable, "user_id=:user_id AND block_id=:block_id", array(
			":user_id"=>$this->owner->id,
			":block_id"=>$user_id,
		));
	}

	public function removeFromScamTable($user_id) {
		return $this->db->createCommand()->delete($this->innerMail->scamTable, "sender_id=:sender_id AND scammer_id=:scammer_id", array(
			":sender_id"=>$this->owner->id,
			":scammer_id"=>$user_id,
		));
	}

	public function inserIntoScamTable($scammer_id, $chain_id) {
		if($scammer_id==$this->owner->id) { return false; }
		$sql = "INSERT IGNORE {$this->innerMail->scamTable} (sender_id, scammer_id, chain_id, complain_date) VALUES (:sender_id, :scammer_id, :chain_id, :complain_date)";
		$command = $this->db->createCommand($sql);
		$sender_id = $this->owner->id;
		$command->bindParam(":sender_id", $sender_id, PDO::PARAM_INT);
		$command->bindParam(":scammer_id", $scammer_id, PDO::PARAM_INT);
		$command->bindParam(":chain_id", $chain_id, PDO::PARAM_STR);
		$command->bindParam(":complain_date", $this->date, PDO::PARAM_STR);
		return $command->execute();
	}


	public function getBlock($block_user_id) {
		if(isset($this->blockStack[$block_user_id])) {
			return $this->blockStack[$block_user_id];
		}
		$sql="SELECT t1.user_id FROM {$this->innerMail->blockTable} as t1 WHERE t1.user_id=".(int) $this->owner->id." AND t1.block_id=".(int) $block_user_id."
					UNION
					SELECT t2.user_id FROM {$this->innerMail->blockTable} as t2 WHERE t2.user_id=".(int) $block_user_id." AND t2.block_id=".(int) $this->owner->id;
		$data=$this->db->createCommand($sql)->queryAll();
		$total=count($data);

		if($total==0) {
			$this->blockStack[$block_user_id]=array(
				'internal'=>false,
				'external'=>false,
			);
		} elseif($total==1) {
			$isEqual=$data[0]['user_id']==$this->owner->id;
			$this->blockStack[$block_user_id] = array(
				'internal'=>$isEqual,
				'external'=>!$isEqual,
			);
		} else {
			$this->blockStack[$block_user_id] = array(
				'internal'=>true,
				'external'=>true,
			);
		}
		return $this->blockStack[$block_user_id];
	}

	public function getBlockedUsers($pgNr, & $pgCnt, & $total) {
		$command=$this->db->createCommand();
		$total=$command
			-> select ("count(*)")
			-> from($this->innerMail->blockTable)
			-> where("user_id=:user_id", array(":user_id"=>$this->owner->id))
			-> queryScalar();
		$command->reset();
		$size=$this->innerMail->blockedUsersPageSize;
		$blocked=array();
		$pgCnt = ceil ($total / $size);
		$pgNr = abs ((int)$pgNr);
		if ($pgNr > 0) $pgNr --;
		if ($pgCnt < $pgNr) return $blocked;
		$offset=$pgNr * $size;

		$dataReader = $this->db->createCommand()
			-> select('block_id, block_date')
			-> from($this->innerMail->blockTable)
			-> where("user_id=:user_id", array(":user_id"=>$this->owner->id))
			-> limit($size, $offset)
			-> order("block_date DESC")
			-> query();

		foreach($dataReader as $row) {
			$blocked[$row['block_id']]=$row;
		}
		return $blocked;
	}
}

