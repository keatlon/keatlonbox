<?
class dbException extends applicationException
{
	public function __construct( $message, $sql)
	{
		parent::__construct($message . "\n". $sql);
		response::exception('Database Error');
	}
}