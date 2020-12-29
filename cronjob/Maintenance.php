<?php

namespace Utils\Cronjob;

abstract class Maintenance extends Task {
	protected function purge(string $table, int $days, string $field='time'){
		$time = $this->get_time($days);
		
		if($this->verbose){
			echo "Purge $table: ".$field.' <= '.$time."\n";
		}
		
		(new \dbdata\Delete)->where([
			$field.' <' => $time
		])->exec($table, 0);
	}
	
	protected function optimize_tables(){
		$dbh = \dbdata\DB::get_dbh();
		$sth = $dbh->prepare("SHOW TABLES");
		$sth->execute();
		$sth->setFetchMode(\PDO::FETCH_NUM);
		while($row = $sth->fetch()){
			$syntax = "OPTIMIZE TABLE `".$row[0]."`\n";
			
			if($this->verbose){
				echo $syntax;
			}
			
			$dbh->prepare($syntax)->execute();
		}
	}
	
	protected function get_time(int $days): int{
		return time() - (60*60*24 * $days);
	}
}