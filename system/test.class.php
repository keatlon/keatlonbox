<?php
class test
{
	static function assert($condition, $message)
	{
		if ($condition)
		{
			$result = '<span style="color:green">ok</span>';
		}
		else
		{
			$result = '<span style="color:red">failed</span>';
		}

		echo '<table width=500 cellspacing=5>
			<tr>
				<td width=400>' . $message . '</td>
				<td>' . $result . '</td>
			</tr>
		</table>';
	}

}

