<?
class dbException extends Exception
{
	public function __construct( $message, $sql)
	{
		response::exception('Database Error');
		log::critical($message . "\n\nSQL: ". $sql . "\n\n" . log::getTraceInfo($this), 'mysql', $this);
	}
}