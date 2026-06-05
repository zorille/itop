<?php
namespace Combodo\iTop\wsclient;

class curl {
	/**
	 * @access protected
	 * @var resource
	 */
	private $connexion;
	/**
	 * @access protected
	 * @var string
	 */
	private $curl;
	/**
	 * @access protected
	 * @var string
	 */
	private $curl_infos;
	/**
	 * @access protected
	 * @var string
	 */
	private $code_retour_curl = 200;
	/**
	 * @access protected
	 * @var boolean
	 */
	private $valide_code_retour = true;
	/**
	 * @access protected
	 * @var string
	 */
	private $UAgent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:90.0) Gecko/20100101 Firefox/90.0";

	// private $UAgent = "Mozilla/5.0 (Windows NT 6.0; rv:5.0) Gecko/20100101 Firefox/5.0";

	/**
	 * @codeCoverageIgnore
	 * @param string $nom_module
	 * @param string $sort_en_erreur
	 */
	public function __construct() {
	}

	/**
	 * Se connecte au Web Service
	 * @param $url
	 * @return curl
	 */
	public function &connectService(
			$url): static
	{
		$this->setCurl ( $url );
		return $this->setConnexion ( curl_init ( $this->getCurl () ) );
	}

	/**
	 * Transmet la requete Curl
	 * @codeCoverageIgnore
	 * @return mixed|false en cas d'erreur
	 * @throws Exception
	 */
	public function send_curl(): mixed
	{
		$this->setCurlUserAgent ();
		$retour = curl_exec ( $this->getConnexion () );
		$this->curl_getinfo ();
		if ($retour === false) {
			$curl_no = curl_errno ( $this->getConnexion () );
			switch ($curl_no) {
				case 51 :
					//$this->onWarning ( "cURL[" . curl_errno ( $this->getConnexion () ) . "] " . curl_error ( $this->getConnexion () ) );
					break;
				default :
					return false;//$this->onError ( "cURL[" . curl_errno ( $this->getConnexion () ) . "] " . curl_error ( $this->getConnexion () ), curl_getinfo ( $this->getConnexion () ), curl_errno ( $this->getConnexion () ) );
			}
		}
		/* Test des codes retour HTTP. */
		if ($this->getValideCodeErreur ()) {
			$this->test_code_retour ( $retour );
		}
		return $retour;
	}

	/**
	 * Test les code retour d'erreur standard.
	 * @return false|curl
	 * @throws Exception
	 */
	public function test_code_retour(
			$retour): bool|curl|static
	{
		/* Test des codes retour HTTP. */
		$httpCode = $this->curl_getinfo ();
		if ($httpCode == 404) {
			return false;//$this->onError ( "Erreur HTTP : " . $httpCode, $retour, $httpCode );
		}
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 * @param string $nom_local
	 * @param boolean $mode_ascii
	 * @param bool|string $chmod
	 * @return mixed|false
	 * @throws Exception
	 */
	public function ftp_curl_put(
		string      $nom_local,
		bool        $mode_ascii = FALSE,
		bool|string $chmod = FALSE): mixed
	{
		$ret = FALSE;
		if (is_file ( $nom_local )) {
			$fp = fopen ( $nom_local, 'r' );
			curl_setopt ( $this->getConnexion (), CURLOPT_INFILE, $fp );
			curl_setopt ( $this->getConnexion (), CURLOPT_INFILESIZE, filesize ( $nom_local ) );
			curl_setopt ( $this->getConnexion (), CURLOPT_UPLOAD, TRUE );
			if ($mode_ascii) {
				curl_setopt ( $this->getConnexion (), CURLOPT_TRANSFERTEXT, TRUE );
			}
			if ($chmod) {
				$path = parse_url ( $this->getCurl (), PHP_URL_PATH );
				curl_setopt ( $this->getConnexion (), CURLOPT_POSTQUOTE, array (
						"SITE CHMOD $chmod $path"
				) );
			}
			$ret = $this->send_curl ();
			fclose ( $fp );
		}
		return $ret;
	}

	/**
	 * Telecharge un fichier
	 * @codeCoverageIgnore
	 * @param string $sortie Fichier de sortie des donnees telechargees
	 * @return mixed|false
	 * @throws Exception
	 */
	public function ftp_curl_get(
		string $sortie): mixed
	{
		if ($fp = fopen ( $sortie, 'w' )) {
			curl_setopt ( $this->getConnexion (), CURLOPT_FILE, $fp );
			$ret = $this->send_curl ();
			fclose ( $fp );
			return $ret;
		}
		return FALSE;
	}

	/**
	 * Lister les fichiers d'un ftp
	 * @codeCoverageIgnore
	 * @return mixed|false
	 * @throws Exception
	 */
	public function ftp_curl_list(): mixed
	{
		$this->setReturnTransfert ( TRUE );
		curl_setopt ( $this->getConnexion (), CURLOPT_FTPLISTONLY, TRUE );
		return $this->send_curl ();
	}

	/**
	 * Lister les fichiers d'un ftp
	 * @codeCoverageIgnore
	 * @return mixed|false
	 * @throws Exception
	 */
	public function curl_getinfo(): mixed
	{
		$this->setCurlInfos ( curl_getinfo ( $this->getConnexion () ) );
		$code_retour = curl_getinfo ( $this->getConnexion (), CURLINFO_HTTP_CODE );
		$this->setCodeRetourCurl ( $code_retour );
		return $code_retour;
	}

	/**
	 * Active le connect Time Out
	 * @codeCoverageIgnore
	 * @return curl
	 * @throws Exception
	 */
	public function curl_connecttimeout(
			$time = 1): static
	{
		$this->setReturnTransfert ( TRUE );
		curl_setopt ( $this->getConnexion (), CURLOPT_CONNECTTIMEOUT, $time );
		return $this;
	}

	/**
	 * Ferme la connexion
	 * @codeCoverageIgnore
	 * @return curl
	 */
	public function close(): static
	{
		curl_close ( $this->getConnexion () );
		return $this;
	}

	/**
	 * ****************** Accesseurs ****************
	 */
	/**
	 * @codeCoverageIgnore
	 */
	public function getConnexion() {
		return $this->connexion;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setConnexion(
			$connexion): static
	{
		$this->connexion = $connexion;
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function getCurl(): string
	{
		return $this->curl;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setCurl(
			$url): static
	{
		$this->curl = $url;
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function getCodeRetourCurl(): int|string
	{
		return $this->code_retour_curl;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setCodeRetourCurl(
			$code_retour_curl): static
	{
		$this->code_retour_curl = $code_retour_curl;
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function getUAgent(): string
	{
		return $this->UAgent;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setUAgent(
			$UAgent): static
	{
		$this->UAgent = $UAgent;
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setEpsv(
			$use = true): static
	{
		curl_setopt ( $this->getConnexion (), CURLOPT_FTP_USE_EPSV, $use );
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setSslVerifyPeerAndHost(
			$actif): static
	{
		curl_setopt ( $this->getConnexion (), CURLOPT_SSL_VERIFYPEER, $actif );
		curl_setopt ( $this->getConnexion (), CURLOPT_SSL_VERIFYHOST, $actif );
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setHttpHAuth(
			$type = "any"): static
	{
		switch ($type) {
			case "any" :
				curl_setopt ( $this->getConnexion (), CURLOPT_HTTPAUTH, CURLAUTH_ANY );
				break;
			case "basic" :
				curl_setopt ( $this->getConnexion (), CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
				break;
		}
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setHttpHeader(
			$headers): static
	{
		curl_setopt ( $this->getConnexion (), CURLOPT_HTTPHEADER, $headers );
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setHeader(
			$actif): static
	{
		if (is_bool ( $actif )) {
			curl_setopt ( $this->getConnexion (), CURLOPT_HEADER, $actif );
		}
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setReturnTransfert(
			$actif): static
	{
		if (is_bool ( $actif )) {
			curl_setopt ( $this->getConnexion (), CURLOPT_RETURNTRANSFER, $actif );
		}
		return $this;
	}

	/**
	 * pour suivre tous les en-tetes "Location: " que le serveur envoie dans les en-tetes HTTP
	 * @codeCoverageIgnore
	 */
	public function &setLocation(
			$actif): static
	{
		if (is_bool ( $actif )) {
			curl_setopt ( $this->getConnexion (), CURLOPT_FOLLOWLOCATION, $actif );
		}
		return $this;
	}

	/**
	 * pour suivre tous les en-tetes "Location: " que le serveur envoie dans les en-tetes HTTP
	 * @codeCoverageIgnore
	 */
	public function &setFollowRedirections(): self
    {
        curl_setopt ( $this->getConnexion (), CURLOPT_POSTREDIR, CURL_REDIR_POST_ALL );
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setUseDns(
			$actif): static
	{
		if (is_bool ( $actif )) {
			curl_setopt ( $this->getConnexion (), CURLOPT_DNS_USE_GLOBAL_CACHE, $actif );
		}
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setCookie(
			$cookie): static
	{
		if ($cookie != "") {
			curl_setopt ( $this->getConnexion (), CURLOPT_COOKIE, $cookie );
		}
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setCurlUserAgent(): static
	{
		curl_setopt ( $this->getConnexion (), CURLOPT_USERAGENT, $this->getUAgent () );
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setUserPasswd(
			$user,
			$passwd): static
	{
		if ($user != "" && $passwd != "") {
			curl_setopt ( $this->getConnexion (), CURLOPT_USERPWD, $user . ":" . $passwd );
		}
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setReferer(
			$referer): static
	{
		if ($referer != "") {
			curl_setopt ( $this->getConnexion (), CURLOPT_REFERER, $referer );
		}
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setProxy(
			$adresse_proxy,
			$port_proxy,
			$login_proxy,
			$passwd_proxy,
			$type_proxy): static
	{
		curl_setopt ( $this->getConnexion (), CURLOPT_HTTPPROXYTUNNEL, true );
		curl_setopt ( $this->getConnexion (), CURLOPT_PROXY, $adresse_proxy );
		curl_setopt ( $this->getConnexion (), CURLOPT_PROXYPORT, $port_proxy );
		curl_setopt ( $this->getConnexion (), CURLOPT_PROXYTYPE, $type_proxy );
		// CURLOPT_PROXYUSERPWD "[username]:[password]"
		// CURLOPT_PROXYAUTH CURLAUTH_BASIC et CURLAUTH_NTLM
		// CURLOPT_PROXYTYPE Soit CURLPROXY_HTTP (0 par défaut), soit CURLPROXY_SOCKS4 (4), soit CURLPROXY_SOCKS5 (5), soit CURLPROXY_SOCKS5_HOSTNAME (7 but no constant defined)
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setNoProxy(): static
	{
		curl_setopt ( $this->getConnexion (), CURLOPT_HTTPPROXYTUNNEL, false );
		curl_setopt ( $this->getConnexion (), CURLOPT_PROXY, "" );
		curl_setopt ( $this->getConnexion (), CURLOPT_PROXYPORT, "" );
		curl_setopt ( $this->getConnexion (), CURLOPT_PROXYTYPE, "CURLPROXY_HTTP" );
		// CURLOPT_PROXYUSERPWD "[username]:[password]"
		// CURLOPT_PROXYAUTH CURLAUTH_BASIC et CURLAUTH_NTLM
		// CURLOPT_PROXYTYPE Soit CURLPROXY_HTTP (par défaut), soit CURLPROXY_SOCKS5 (5), soit CURLPROXY_SOCKS5_HOSTNAME (7 but no constant defined)
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setPostData(
			$postData): static
	{
		if ($postData != "") {
			curl_setopt ( $this->getConnexion (), CURLOPT_POST, true );
			curl_setopt ( $this->getConnexion (), CURLOPT_POSTFIELDS, $postData );
		}
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setTimeout(
			$timeout): static
	{
		if ($timeout != "") {
			curl_setopt ( $this->getConnexion (), CURLOPT_TIMEOUT, $timeout );
			curl_setopt ( $this->getConnexion (), CURLOPT_CONNECTTIMEOUT, $timeout );
		}
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setRequest(
			$request): static
	{
		if ($request != "") {
			$this->setReturnTransfert ( TRUE );
			curl_setopt ( $this->getConnexion (), CURLOPT_CUSTOMREQUEST, $request );
		}
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setVerbose(): static
	{
		curl_setopt ( $this->getConnexion (), CURLOPT_VERBOSE, TRUE );
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setOptionArray(
			$option_array): static
	{
		curl_setopt_array ( $this->getConnexion (), $option_array );
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function getValideCodeErreur(): bool
	{
		return $this->valide_code_retour;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setValideCodeErreur(
			$valide_code_erreur): static
	{
		$this->valide_code_retour = $valide_code_erreur;
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function getCurlInfos(): string
	{
		return $this->curl_infos;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setCurlInfos(
			$curl_infos): static
	{
		$this->curl_infos = $curl_infos;
		return $this;
	}

	/**
	 * ****************** Accesseurs ****************
	 */
}

/**
 * class wsclient<br> Renvoi des information via un webservice.
 * @package Lib
 * @subpackage WebService
 */
class wsclient {
	/**
	 * var privee
	 * @access private
	 * @var string
	 */
	private $url = "";
	/**
	 * var privee
	 * @access private
	 * @var array
	 */
	private $params = array ();
	/**
	 * var privee
	 * @access private
	 * @var string
	 */
	private $post_datas = "";
	/**
	 * var privee
	 * @access private
	 * @var string
	 */
	private $http_method = "GET";
	/**
	 * var privee
	 * @access private
	 * @var string
	 */
	private $http_entete = "";
	/**
	 * var privee
	 * @access private
	 * @var string
	 */
	private $content_type = "text/plain";
	/**
	 * var privee
	 * @access private
	 * @var string
	 */
	private $accept = "text/plain";
	/**
	 * @access protected
	 * @var int
	 */
	private $connection_timeout = 120;
	/**
	 * var privee
	 * @access private
	 * @var string
	 */
	private $no_connexion = false;
	/**
	 * var privee
	 * @access private
	 * @var boolean
	 */
	private $validSSLcert = false;
	/**
	 * var privee
	 * @access private
	 * @var string
	 */
	private $httpAuth = 'any';
	/**
	 * @access private
	 * @var curl
	 */
	private $objet_curl = null;
	/**
	 * var privee
	 * @access private
	 * @var string
	 */
	private $force_param_url = false;
	/**
	 * var privee
	 * @access private
	 * @var string
	 */
	private $collect_header = false;
	/**
	 * var privee
	 * @access private
	 * @var string
	 */
	private $header_data = "";
	/**
	 * var privee
	 * @access private
	 * @var string
	 */
	private $curl_info = "";

	/**
	 * Constructeur.
	 * @codeCoverageIgnore
	 */
	public function __construct() {
		$this->setObjetCurl ( new curl ( ) );
		return true;
	}

	/**
	 * Valide la presence des variables obligatoires dans un tableau de definition du serveur.
	 * @return wsclient|false l'objet wsclient si OK, False sinon
	 * @throws Exception
	 */
	public function retrouve_variables_tableau(
			$serveur_data): bool|wsclient|static
	{
		if (! isset ( $serveur_data ["url"] )) {
			return false;
			//return $this->onError ( "Il faut un champ url dans la definition du serveur", "", 5100 );
		} else {
			$this->setUrl ( $serveur_data ["url"] );
		}
		if (isset ( $serveur_data ["RequestTimeout"] )) {
			$this->setConnexionTimeout ( $serveur_data ["RequestTimeout"] );
		}
		return $this;
	}

	/**
	 * Creation d'entete HTTP standard
	 * @return wsclient
	 */
	public function prepare_html_entete(): static
	{
		return $this->setHttpHeader ( array (
				"Content-Type: " . $this->getContentType (),
				"Accept: " . $this->getAccept ()
		) );
	}

	/**
	 * Nettoie le retour JSon contenant {"message":"","success":true,"return_code":0}
	 * @param string $retour_json
	 * @param boolean $return_array
	 * @return mixed
	 */
	public function traite_retour_json(
		string $retour_json,
		bool   $return_array = true): mixed
	{
		// Si le json contient l'ajout du framework, on le traite separement
		if (str_contains($retour_json, '{"message":"","success":true,"return_code":0}')) {
			$retour_json = str_replace ( '{"message":"","success":true,"return_code":0}', "", $retour_json );
			$tableau_resultat = json_decode ( $retour_json, true );
			$tableau_resultat ["success"] = true;
			$tableau_resultat ["return_code"] = 0;
			$tableau_resultat ["message"] = "";
		} else {
			$tableau_resultat = json_decode ( $retour_json, $return_array );
		}
		return $tableau_resultat;
	}

	/**
	 * Envoi la requete de type CuRL par defaut et attend un retour CuRL.
	 *
	 * @return bool|stdClass|string|array resultat du json ou false en cas d'erreur
	 * @throws Exception
	 */
	public function envoi_requete(): mixed
	{
		$url = $this->prepare_url_standard ();
		// Si la connexion est desactive par le parametre no_wsclient
		if ($this->getNoconnexion () === true) {
			return array ();
		}
		// On prepare le Curl
		$this->getObjetCurl ()
			->connectService ( $url );
		try {
			$this->gere_curl_options ();
			// On applique la requete
			$retour_curl = $this->getObjetCurl ()
				->send_curl ();
			if ($this->getCollectHeader ()) {
				$this->setCurlInfo ( $this->getObjetCurl ()
					->getCurlInfos () );
				$header_size = $this->getCurlInfo () ['header_size'];
				$this->setHeaderData ( substr ( $retour_curl, 0, $header_size ) );
				$retour_curl = substr ( $retour_curl, $header_size );
			}
		} catch ( Exception $e ) {
			return false; //$this->onError ( "Requete " . $url . " en erreur", $e->getMessage (), 4500 );
		}
		// On ferme la connexion
		$this->getObjetCurl ()
			->close ();
		return $retour_curl;
	}

	/**
	 * Ajoute un hearder HTTP. Par defaut : Content-Type: application/json Necessite un connexion curl active
	 * @return wsclient
	 */
	public function gere_header(): static
	{
		// On ajoute les donnees sur une connexion active uniquement
		$header = $this->getHttpHeader ();
		if ($header == '') {
			$header = "Content-Type: application/json";
		}
		if (! is_array ( $header )) {
			$header = array (
					$header
			);
		}
		$this->getObjetCurl ()
			->setHttpHeader ( $header );
		return $this;
	}

	/**
	 * Valide les options fournit en argument
	 * @return wsclient
	 * @throws Exception
	 */
	public function gere_curl_options(): static
	{
		// On gere les differents parmetres d'une requete
		// le besoin d'avoir le header dans la reponse
		if ($this->getCollectHeader ()) {
			$this->getObjetCurl ()
				->setHeader ( true )
				->setReturnTransfert ( true );
		}
		// le Verbose
		if ($this->getListeOptions ()
			->getOption ( "verbose" ) == 3) {
			$this->getObjetCurl ()
				->setVerbose ();
		}
		// le Connect Time Out
		if ($this->getListeOptions ()
			->verifie_option_existe ( "curl_connecttimeout" ) !== false) {
			$this->getObjetCurl ()
				->curl_connecttimeout ( $this->getListeOptions ()
				->getOption ( "curl_connecttimeout" ) );
		}
		// le Follow header Location:
		if ($this->getListeOptions ()
			->verifie_option_existe ( "curl_nofollowlocation" ) === false) {
			$this->getObjetCurl ()
				->setLocation ( true );
		}
		//gestion des utilisateurs et du proxy
		$this->gere_request ()
			->gere_utilisateurs ()
			->gere_header ();
		// On invalide le check SSL du certificat si necessaire
		if ($this->getValidSSL () === false) {
			$this->getObjetCurl ()
				->setSslVerifyPeerAndHost ( false );
		}
		// on gere la redirection du post
		if (
			in_array(
				$this->getHttpMethod (),
				["POST", "PUT", "PATCH"]
			)
		) {
			$this->getObjetCurl()
				->setLocation(true)
				->setFollowRedirections();
		}
		return $this;
	}

	/**
	 * Gere le type de request (GET, POST, PUt ou DELETE) En cas de request differente de GET : ajoute les donnees en mode POSTDATA Necessite un connexion curl active
	 * @return wsclient
	 */
	public function gere_request(): static
	{
		$this->getObjetCurl ()
			->setRequest ( $this->getHttpMethod () );
		if ($this->getHttpMethod () != "GET") {
			$this->gere_post_data ();
		}
		return $this;
	}

	/**
	 * Ajoute les donnees en mode POSTDATA si la request est de type POST Necessite un connexion curl active
	 * @return wsclient
	 */
	public function gere_post_data(): static
	{
		if ($this->getHttpMethod () == "POST" || $this->getHttpMethod () == "PUT" || $this->getHttpMethod () == "PATCH") {
			if ($this->getPostDatas () != "") {
				$this->getObjetCurl ()
					->setPostData ( $this->getPostDatas () );
			} else {
				$this->getObjetCurl ()
					->setPostData ( $this->getParams () );
			}
		}
		return $this;
	}

	/**
	 * Ajoute l'utilisateur et son mon de passe dans le header HTTP Necessite un connexion curl active
	 * @return wsclient
	 */
	public function gere_utilisateurs(): static
	{
		// Si un User/Pass est defini
		/*if ($this->getGestionConnexionUrl ()
			->getObjetUtilisateurs ()
			->getUsername () !== "") {
			$this->getObjetCurl ()
				->setUserPasswd ( $this->getGestionConnexionUrl ()
				->getObjetUtilisateurs ()
				->getUsername (), $this->getGestionConnexionUrl ()
				->getObjetUtilisateurs ()
				->getPassword () )
				->setHttpHAuth ( $this->getHttpAuth () );
		}*/
		return $this;
	}

	/**
	 * Construit une url standard a partir du getHost et getUrl.<br/> Choisi entre la methode GET ou POST
	 * @return string url construite
	 */
	public function prepare_url_standard(): string
	{
		if ($this->getHttpMethod () == "GET" || $this->getForceParamInUrl ()) {
			return $this->prepare_url_get ();
		}
		// On ajoute les donnees post sur une connexion active uniquement
		return $this->getUrl ();
	}

	/**
	 * Construit une url standard a partir du getHost et getUrl + la liste des parametres en GET.
	 * @return string url construite
	 */
	public function prepare_url_get(): string
	{
		$url = $this->getUrl ();
		if (count ( $this->getParams () ) !== 0) {
			$url .= "?" . http_build_query ( $this->getParams () );
		}
		return $url;
	}

	/**
	 * *********************** Accesseurs **********************
	 */
	/**
	 * @codeCoverageIgnore
	 */
	public function getUrl(): string
	{
		return $this->url;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setUrl(
			$url): static
	{
		$this->url = $url;
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function getNoconnexion(): bool|string
	{
		return $this->no_connexion;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setNoconnexion(
			$no_connexion): static
	{
		if (is_bool ( $no_connexion )) {
			$this->no_connexion = $no_connexion;
		}
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function getParams(): array
	{
		return $this->params;
	}

	/**
	 * Param est obligatoirement un array, en cas de string type url utiliser setPostDatas
	 * @codeCoverageIgnore
	 */
	public function &setParams(
			$param,
			$value = "",
			$add = false): static
	{
		if ($add) {
			$this->params [$param] = $value;
		} else {
			if (is_array ( $param )) {
				$this->params = $param;
			} else {
				$this->params = array (
						$param => $value
				);
			}
		}
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function getPostDatas(): string|array
	{
		return $this->post_datas;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setPostDatas(
			$post_datas): static
	{
		$this->post_datas = $post_datas;
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function getHttpMethod(): string
	{
		return $this->http_method;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setHttpMethod(
			$http_method): static
	{
		$this->http_method = strtoupper ( $http_method );
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function getHttpHeader(): string|array
	{
		return $this->http_entete;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setHttpHeader(
			$http_entete): static
	{
		$this->http_entete = $http_entete;
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function getContentType(): string
	{
		return $this->content_type;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setContentType(
			$content_type): static
	{
		$this->content_type = $content_type;
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function getAccept(): string
	{
		return $this->accept;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setAccept(
			$accept): static
	{
		$this->accept = $accept;
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function getConnexionTimeout(): int
	{
		return $this->connection_timeout;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setConnexionTimeout(
			$connection_timeout): static
	{
		$this->connection_timeout = $connection_timeout;
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function getValidSSL(): bool
	{
		return $this->validSSLcert;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setValidSSL(
			$validSSLcert): static
	{
		$this->validSSLcert = $validSSLcert;
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function getHttpAuth(): string
	{
		return $this->httpAuth;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setHttpAuth(
			$httpAuth): static
	{
		$this->httpAuth = $httpAuth;
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 * @return curl
	 */
	public function &getObjetCurl(): ?curl
	{
		return $this->objet_curl;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setObjetCurl(
			&$curl): static
	{
		$this->objet_curl = $curl;
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function getForceParamInUrl(): bool|string
	{
		return $this->force_param_url;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setForceParamInUrl(
			$force_param_url): static
	{
		if (is_bool ( $force_param_url )) {
			$this->force_param_url = $force_param_url;
		}
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &getCollectHeader(): bool|string
	{
		return $this->collect_header;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setCollectHeader(
			$collect_header): static
	{
		$this->collect_header = $collect_header;
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &getHeaderData(): string
	{
		return $this->header_data;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setHeaderData(
			$header_data): static
	{
		$this->header_data = $header_data;
		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &getCurlInfo(): string|array
	{
		return $this->curl_info;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function &setCurlInfo(
			$curl_info): static
	{
		$this->curl_info = $curl_info;
		return $this;
	}
}
