<?php
if( ! defined( 'KAHUKPATH' ) ){die(".");}

/**
 * 
 */
function kahuk_mysql_server_version() {
    global $kahukDB;

    $pattern = '/[^0-9-.]/i';
	$replacement = '';
	$mysqlServerVersion = $kahukDB->server_info;
	$mysqlServerVersion = preg_replace( $pattern, $replacement, $mysqlServerVersion );

	if ( strpos( $mysqlServerVersion, '-' ) > 0 ) { 
		$mysqlServerVersion = strstr( $mysqlServerVersion, '-', true );
	} else {
		$mysqlServerVersion = $mysqlServerVersion;
	}

    return $mysqlServerVersion;
}


/**
 * Load the database class file and instantiate the `$wpdb` global.
 *
 * @since 5.0.0
 *
 * @global kahukDB $kahukDB WordPress database abstraction object.
 */
function require_kahuk_db() {
	global $kahukDB;

	$db = new KahukSetupDB( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST );
    $kahukDB = $db->db_connect();
}

/**
 * D atabase Class for setup only
 * 
 * @since 5.0.0
 * 
 */
class KahukSetupDB {
	/**
	 * Database Username
	 *
	 * @since 2.9.0
	 * @var string
	 */
	protected $dbuser;

	/**
	 * Database Password
	 *
	 * @since 3.1.0
	 * @var string
	 */
	protected $dbpassword;

	/**
	 * Database Name
	 *
	 * @since 3.1.0
	 * @var string
	 */
	protected $dbname;

	/**
	 * Database Host
	 *
	 * @since 5.0.0
	 * @var string
	 */
	protected $dbhost;


    public function __construct( $dbuser, $dbpassword, $dbname, $dbhost ) {
		$this->dbuser     = $dbuser;
		$this->dbpassword = $dbpassword;
		$this->dbname     = $dbname;
		$this->dbhost     = $dbhost;
		$this->dbhost     = $dbhost;
	}

    /**
     * 
     */
    public function db_connect() {
        $dbh = new mysqli( $this->dbhost, $this->dbuser, $this->dbpassword, $this->dbname );

        if ( mysqli_connect_errno() ) {
            printf( "Connect failed: %s\n", mysqli_connect_error() );
            exit();
        }

        return $dbh;
    }
}

// Include the wpdb class and, if present, a db.php database drop-in.
global $kahukDB;
require_kahuk_db();



/**
 * 
 */
function kahuk_check_table_exists( $table ) {
	global $kahukDB;

	$result = $kahukDB->query( "SHOW TABLES LIKE '".$table."';" );
	$numRows = $result->num_rows;
	
	if ( $numRows < 1 ) {
		return false;
	} else {
		return true;
	}
}
