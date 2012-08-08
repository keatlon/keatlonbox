<?
class dbException extends Exception
{
	public function __construct( $message, $sql)
	{
		response::exception('Database Error');
		log::push($message . "\n". $sql, 'db', log::E_MYSQL, $this);
	}
}