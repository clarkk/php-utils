<?php

namespace Utils\Cmd;

class Proc {
	protected $pid;
	protected $proc;
	
	const PROCSTAT_UTIME 		= 13;
	const PROCSTAT_STIME 		= 14;
	const PROCSTAT_CUTIME 		= 15;
	const PROCSTAT_CSTIME 		= 16;
	const PROCSTAT_PRIORITY 	= 17;
	const PROCSTAT_STARTTIME 	= 21;
	const PROCSTAT_RSS 			= 23;
	
	public function get_nice(?int $pid=null): int{
		if(!$pid && isset($this->proc)){
			$pid = $this->get_pid();
		}
		
		if($nice = $this->get_proc_stat($pid)[self::PROCSTAT_PRIORITY] ?? 0){
			return $nice - 20;
		}
		
		return 0;
	}
	
	public function stat(?int $pid=null): array{
		if(!$pid && isset($this->proc)){
			$pid = $this->get_pid();
		}
		
		$apc_tck 	= 'SYS_CLK_TCK';
		$apc_page 	= 'SYS_PAGESIZE';
		
		$cache_timeout = 60*60*24;
		
		if(!$hertz = apcu_fetch($apc_tck)){
			$hertz = (int)shell_exec('getconf CLK_TCK');
			apcu_store($apc_tck, $hertz, $cache_timeout);
		}
		
		if(!$pagesize_kb = apcu_fetch($apc_page)){
			$pagesize_kb = (int)shell_exec('getconf PAGESIZE') / 1024;
			apcu_store($apc_page, $pagesize_kb, $cache_timeout);
		}
		
		$uptime = explode(' ', file_get_contents('/proc/uptime'))[0];
		
		if(!$procstat = $this->get_proc_stat($pid)){
			return [
				'cpu'	=> '0%',
				'mem'	=> '0M',
				'time'	=> 0
			];
		}
		
		$cputime 	= $procstat[self::PROCSTAT_UTIME] + $procstat[self::PROCSTAT_STIME] + $procstat[self::PROCSTAT_CUTIME] + $procstat[self::PROCSTAT_CSTIME];
		$starttime 	= $procstat[self::PROCSTAT_STARTTIME];
		
		$seconds 	= $uptime - ($starttime / $hertz);
		
		return [
			'cpu'	=> round(($cputime / $hertz / $seconds) * 100, 1).'%',
			'mem'	=> round($procstat[self::PROCSTAT_RSS] * $pagesize_kb / 1024, 2).'M',
			'time'	=> (int)$seconds
		];
	}
	
	private function get_proc_stat(int $pid): array{
		return explode(' ', file_get_contents('/proc/'.$pid.'/stat'));
	}
}