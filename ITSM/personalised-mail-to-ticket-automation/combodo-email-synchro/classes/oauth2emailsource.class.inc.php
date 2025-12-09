<?php
// Copyright (C) 2012-2016 Combodo SARL
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU Lesser General Public License as published by
// the Free Software Foundation; version 3 of the License.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
/**
 * @copyright Copyright (C) 2012-2021 Combodo SARL
 * @license http://opensource.org/licenses/AGPL-3.0
 */
/**
 * Zorille added code
 */
/**
 * Read messages from an IMAP mailbox using PHP's IMAP extension Note: in theory PHP IMAP methods can also be used to connect to a POP3 mailbox, but in practice the missing emulation of actual unique identifiers (UIDLs) for the messages makes this unusable for our particular purpose
 */
class OAuth2EmailSource extends EmailSource {
	protected $rImapConn = null;
	protected $sLogin = '';
	protected $sMailbox = '';
	protected $sTargetFolder = '';
	private $liste_options;
	private $o365_message;
	private $messages = array ();
	private $ref_UUID_internal_id = array ();

	public function __construct(
			$sServer,
			$iPort,
			$sLogin,
			$sPwd,
			$sMailbox,
			$aOptions,
			$sTargetFolder = '') {
		parent::__construct ();
		$this->sLastErrorSubject = '';
		$this->sLastErrorMessage = '';
		$this->sLogin = $sLogin;
		$this->sMailbox = $sMailbox;
		$this->sTargetFolder = $sTargetFolder;
		$this->init_zorille_class ();
		$o365_webservice = \Zorille\o365\wsclient::creer_wsclient ( $this->liste_options, \Zorille\o365\datas::creer_datas ( $this->liste_options ) );
		$o365_webservice->prepare_connexion ( $sServer );
		$this->o365_message = \Zorille\o365\Message::creer_Message ( $this->liste_options, $o365_webservice );
		$this->o365_message->retrouve_userid_par_nom ( $this->sLogin );
	}

	private function init_zorille_class() {
		$rep_document = '/var/www/html/data/TOOLS';
		$argv = array ();
		$argv [0] = "fichier";
		$argv [] .= '--conf';
		$argv [] .= $rep_document . '/conf_clients/o365/prod_client_o365.xml';
		$argv [] .= $rep_document . '/conf_clients/utilisateurs/prod_client_utilisateurs.xml';
		$argv [] .= '--verbose';
		# $argv [] .= '2';
		$argc = count ( $argv );
		require_once $rep_document . '/php_framework/config.php';
		/* Le moteur de iTop en cron supprime toutes les variables mais prend en compte qu'il a deja charge le config.php donc on doit recreer liste_option */
		if (isset ( $argc ) && isset ( $argv ) && ! isset ( $liste_option )) {
			$rep_lib = $rep_document . "/php_framework";
			$liste_option = Zorille\framework\options::creer_options ( $argc, $argv, 0, 20000, "", $rep_lib, true );
		}
		// On met en place les logs
		$fichier_log = Zorille\framework\logs::creer_logs ( $liste_option );
		$fichier_log->setIsWeb ( false );
		$this->liste_options = $liste_option;
		return $this;
	}

	/**
	 * Get the number of messages to process
	 * @return integer The number of available messages
	 */
	public function GetMessagesCount() {
		if (is_object ( $this->o365_message ))
			return $this->o365_message->compte_message ( $this->sMailbox );
		return 0;
	}

	public function getFrom(
			$index,
			$header) {
		if (isset ( $this->messages [$index] ['body']->from )) {
			return array (
					'name' => $this->messages [$index] ['body']->from->emailAddress->name,
					'email' => $this->messages [$index] ['body']->from->emailAddress->address
			);
		} elseif (isset ( $this->messages [$index] ['body']->sender )) {
			return array (
					'name' => $this->messages [$index] ['body']->sender->emailAddress->name,
					'email' => $this->messages [$index] ['body']->sender->emailAddress->address
			);
		} elseif (isset ( $this->messages [$index] ['body']->replyTo )) {
			return array (
					'name' => $this->messages [$index] ['body']->replyTo->emailAddress->name,
					'email' => $this->messages [$index] ['body']->replyTo->emailAddress->address
			);
		}
		return array (
				"name" => "",
				'email' => $header->value
		);
	}

	public function getTo(
			$index,
			$header) {
		$aAddresses = array ();
		if (isset ( $this->messages [$index] ['body']->toRecipients )) {
			foreach ( $this->messages [$index] ['body']->toRecipients as $to ) {
				$pos = count ( $aAddresses );
				$aAddresses [$pos] ['name'] = $to->emailAddress->name;
				$aAddresses [$pos] ['email'] = $to->emailAddress->address;
			}
		}
		return $aAddresses;
	}

	public function getCc(
			$index,
			$header) {
		$aAddresses = array ();
		if (isset ( $this->messages [$index] ['body']->ccRecipients )) {
			foreach ( $this->messages [$index] ['body']->ccRecipients as $pos => $cc ) {
				$aAddresses [$pos] ['name'] = $cc->emailAddress->name;
				$aAddresses [$pos] ['email'] = $cc->emailAddress->address;
			}
		}
		return $aAddresses;
	}

	/**
	 * Prepare header array
	 * @param string $index
	 * @return array
	 */
	public function collect_header(
			$index) {
		$aHeaders = array ();
		foreach ( $this->messages [$index] ['header']->internetMessageHeaders as $header ) {
			switch (strtolower ( $header->name )) {
				case 'from' :
					if (! isset ( $aHeaders ['from'] )) {
						$aHeaders ['from'] = array ();
					}
					$aHeaders ['from'] [count ( $aHeaders ['from'] )] = $this->getFrom ( $index, $header );
					break;
				case 'to' :
					if (! isset ( $aHeaders ['to'] )) {
						$aHeaders ['to'] = array ();
					}
					$aHeaders ['to'] = $this->getTo ( $index, $header );
					break;
				case 'cc' :
					if (! isset ( $aHeaders ['cc'] )) {
						$aHeaders ['cc'] = array ();
					}
					$aHeaders ['cc'] = $this->getCc ( $index, $header );
					break;
				default :
					if (isset ( $aHeaders [strtolower ( $header->name )] )) {
						if (! is_array ( $aHeaders [strtolower ( $header->name )] )) {
							$tempo = $aHeaders [strtolower ( $header->name )];
							$aHeaders [strtolower ( $header->name )] = array ();
							$aHeaders [strtolower ( $header->name )] [] .= $tempo;
						}
						$aHeaders [strtolower ( $header->name )] [] .= $header->value;
					} else {
						$aHeaders [strtolower ( $header->name )] = $header->value;
					}
			}
		}
		return $aHeaders;
	}

	/**
	 * Prepare body array
	 * @param string $index
	 * @return array
	 */
	public function collect_body(
			$index) {
		// hasAttachments
		$sBody = $this->messages [$index] ['body']->body->content;
		return $sBody;
	}

	/**
	 * Prepare body array
	 * @param string $index
	 * @return array
	 */
	public function GetContentType(
			$index) {
		switch ($this->messages [$index] ['body']->body->contentType) {
			case 'html' :
				return 'text/html';
			case 'text' :
				return 'text/plain';
			default :
				return $this->messages [$index] ['body']->body->contentType;
		}
		return "";
	}

	/**
	 * Retrieves the message of the given index [0..Count]
	 * @param $index integer The index between zero and count
	 * @return \MessageFromMailbox
	 */
	public function GetMessage(
			$index) {
		$this->messages [$index] ['header'] = $this->o365_message->lire_header_message ( $this->ref_UUID_internal_id [$index] );
		if (! isset ( $this->messages [$index] ['header']->internetMessageHeaders )) {
			throw new exception ( 'HEADER ' . print_r ( $this->messages [$index] ['header'], true ) );
		}
		$this->messages [$index] ['body'] = $this->o365_message->lire_message ( $this->ref_UUID_internal_id [$index] );
		if (! isset ( $this->messages [$index] ['body']->body )) {
			throw new exception ( 'BODY ' . print_r ( $this->messages [$index] ['body'], true ) );
		}
		$sRawHeaders = $this->collect_header ( $index );
		$aPart = array (
				'type' => ''
		);
		$aPart ['headers'] = $sRawHeaders;
		$aPart ['body'] = $this->collect_body ( $index );
		$aPart ['content-type'] = $this->GetContentType ( $index );
		$aPart ['parts'] = array ();
		if ($this->messages [$index] ['body']->hasAttachments == 1) {
			$aPart ['type'] = 'simple';
			$liste_attachments = $this->o365_message->liste_attachments ( $this->messages [$index] ['body']->id );
			foreach ( $liste_attachments->value as $attachment ) {
				if (empty ( $attachment->contentId )) {
					$attachment->contentId = "itop_" . count ( $aPart ['parts'] );
				}
				$aPart ['parts'] [count ( $aPart ['parts'] )] = array (
						'filename' => $attachment->name,
						'mimeType' => $attachment->contentType,
						'content-id' => $attachment->contentId,
						'content' => base64_decode ( $attachment->contentBytes ),
						'inline' => $attachment->isInline
				);
			}
		}
		$sRawBody = $this->o365_message->lire_mimeType_message ( $this->ref_UUID_internal_id [$index] );
		$ParsingMessage = new MessageFromMailbox ( $this->ref_UUID_internal_id [$index], "", "" );
		$ParsingMessage->SetRawContent ( $sRawBody )
			->SetaParts ( $aPart )
			->SetaHeaders ( $sRawHeaders )
			->setSource ( 'oauth2' );
		return $ParsingMessage;
	}

	/**
	 * Deletes the message of the given index [0..Count] from the mailbox
	 * @param $index integer The index between zero and count
	 */
	public function DeleteMessage(
			$uIDl) {
		if (is_integer ( $uIDl )) {
			// Si on fournit un index, on retrouve le uIDl
			$this->o365_message->supprime_message ( $this->ref_UUID_internal_id [$uIDl] );
		} else {
			$this->o365_message->supprime_message ( $uIDl );
		}
		return null;
	}

	/**
	 * Move the message of the given index [0..Count] from the mailbox to another folder
	 * @param $index integer The index between zero and count
	 */
	public function MoveMessage(
			$index) {
	}

	/**
	 * Name of the eMail source
	 */
	public function GetName() {
		return $this->sLogin;
	}

	/**
	 * Mailbox path of the eMail source
	 */
	public function GetMailbox() {
		return $this->sMailbox;
	}

	/**
	 * Get the list (with their IDs) of all the messages
	 * @return Array An array of hashes: 'msg_id' => index 'uild' => message identifier
	 */
	public function GetListing() {
		$ret = null;
		if (is_object ( $this->o365_message )) {
			$nbmessage = $this->GetMessagesCount ();
			$aResponse = $this->o365_message->lire_liste_message ( $this->sMailbox, array (
					'$top' => $nbmessage
			) );
			$ret = array ();
			foreach ( $aResponse->value as $aMessage ) {
				$pos = count ( $ret );
				$ret [$pos] = array (
						'msg_id' => $aMessage->id,
						'uidl' => $aMessage->id
				);
				$this->ref_UUID_internal_id [$pos] = $aMessage->id;
			}
		}
		return $ret;
	}

	public function Disconnect() {
	}
}
