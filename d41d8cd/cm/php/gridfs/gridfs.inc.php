<?php
namespace Cm;

class GridFsDirect {
	private $m_db=null;
	private $m_prefix="gfs";
	private $m_gfs_rs="";
	private $m_gfs_chunks="";

	function __construct(&$db,$prefix="gfs"){
		$this->m_db = $db;
		$this->m_prefix = $prefix;
		$this->m_gfs_rs="{$prefix}_rs";
		$this->m_gfs_chunks="{$prefix}_chunks";
	}

	public function clean(){
		$ca=new DbQuery($this->m_db);
		$sql="delete from {$this->m_gfs_rs} where rsname='' and rstype='' and rsdatetime < current_timestamp - '1 hour'::interval";
		$ca->exec($sql);
	}


	public function storeBytes($bytes,$p=array()){
		$ca=new DbQuery($this->m_db);
		$p = (object) $p;


		$p->rsname = isset($p->rsname) ? $p->rsname:"";
		$p->rstype = isset($p->rstype) ? $p->rstype:"";
		$p->rssize = isset($p->rssize) ? $p->rssize:"0";
		$p->rssize = $p->rssize>0 ? $p->rssize:strlen($bytes);


		$p->relname = isset($p->relname) ? $p->relname:"";
		$p->relkey = isset($p->relkey) ? $p->relkey:"";
		$p->relvalue = isset($p->relvalue) ? $p->relvalue:"";
		$p->relfield = isset($p->relfield) ? $p->relfield:"";

		$dbDriver=strtolower( $this->m_db->driver() );

		$sql="insert into {$this->m_gfs_rs}
		(rsname,rstype,rssize,relname,relkey,relvalue,relfield) values
		(:rsname,:rstype,:rssize,:relname,:relkey,:relvalue,:relfield)
		";

		if( $dbDriver=="pgsql" ){
			$sql.=" returning rsid";
		}

		$ca->prepare($sql);

		$ca->bindValue(":rsname",$p->rsname,true);
		$ca->bindValue(":rstype",$p->rstype,true);
		$ca->bindValue(":rssize",$p->rssize,false);

		$ca->bindValue(":relname",$p->relname,true);
		$ca->bindValue(":relkey",$p->relkey,true);
		$ca->bindValue(":relvalue",$p->relvalue,true);
		$ca->bindValue(":relfield",$p->relfield,true);


		$ca->exec();

		if( $dbDriver=="pgsql" ){
			$rs = $ca->fetch();
		}
		else {
			$sql="select last_insert_id() as rsid";
			$ca->exec($sql);
			$rs=$ca->fetch();
		}

		
		
		if(!empty($bytes) ){
			//data:image/jpg;base64,/9j/4AAQ
			
			if( strpos($bytes,"base64") !== false ){
				$tmp = explode(";base64,",$bytes);
				$base64 = $tmp[1];
			}
			else {
				$base64=base64_encode($bytes);
			}
			
			//\Application::log("base64=".strlen($base64) );
			//$chks=str_split($base64, (256*1024));
			//$chks=implode("",$chks);
			//$chks=array($chks);
			//\Application::log($base64);

			$chks = str_split($base64, (128*1024) ); //256k segment
			//$chks = array($base64);
			//\Application::log("chks=".count($chks) );

			//file_put_contents("/tmp/image.b64","");
			foreach($chks as $k=>$data){
				//file_put_contents("/tmp/image.b64",$data."\n",FILE_APPEND);

				//$data=pg_escape_bytea($data);
				//$data=base64_encode($data);
				$sql="insert into {$this->m_gfs_chunks} (rsid,segment,data) values (:rsid,:segment,:data)";
				$ca->prepare($sql);
				$ca->bindValue(":rsid",$rs->rsid,false);
				$ca->bindValue(":segment",$k,false);
				$ca->bindValue(":data",$data,true);

				$ca->exec();
			}
		}


		return $rs->rsid;
	}

	public function storeFile($filename,$extra=array()){
		if( !file_exists($filename) ){
			throw new PublicException("File not found {$filename}");
		}

		$bytes = file_get_contents($filename);
		return $this->storeBytes($bytes,$extra);
	}



	public function updateRsById($rsId,$p=array()){
		$ca=new DbQuery($this->m_db);
		$p = (object) $p;


		$p->rsname = isset($p->rsname) ? $p->rsname:"";
		$p->rstype = isset($p->rstype) ? $p->rstype:"";
		//$p->rssize = isset($p->rssize) ? $p->rssize:"0";
		$p->rssize = "rssize";

		$p->relname = isset($p->relname) ? $p->relname:"";
		$p->relkey = isset($p->relkey) ? $p->relkey:"";
		$p->relvalue = isset($p->relvalue) ? $p->relvalue:"";
		$p->relfield = isset($p->relfield) ? $p->relfield:"";

		$sql="update {$this->m_gfs_rs}
		set
			rsname=:rsname,
			rstype=:rstype,
			rssize=:rssize,
			relname=:relname,
			relkey=:relkey,
			relvalue=:relvalue,
			relfield=:relfield
		where rsid=:rsid";


		$ca->prepare($sql);
		$ca->bindValue(":rsid",$rsId,true);
		$ca->bindValue(":rsname",$p->rsname,true);
		$ca->bindValue(":rstype",$p->rstype,true);
		$ca->bindValue(":rssize",$p->rssize,false);

		$ca->bindValue(":relname",$p->relname,true);
		$ca->bindValue(":relkey",$p->relkey,true);
		$ca->bindValue(":relvalue",$p->relvalue,true);
		$ca->bindValue(":relfield",$p->relfield,true);
		//throw new PublicException($ca->preparedQuery());

		$ca->exec();
		return;
	}



	public function loadByRel($relname,$relkey,$relvalue,$relfield='*'){
		$ca=new DbQuery($this->m_db);
		$sql="select * from {$this->m_gfs_rs}
		where relname=:relname and relkey=:relkey and relvalue=:relvalue
		and relfield = ( case when :relfield = '*' then relfield else :relfield end )
		order by rsname";


		$ca->prepare($sql);
		$ca->bindValue(":relname",$relname,true);
		$ca->bindValue(":relkey",$relkey,true);
		$ca->bindValue(":relvalue",$relvalue,true);
		$ca->bindValue(":relfield",$relfield,true);
		$ca->exec();

		return $ca->fetchAll();
	}

	public function loadById($rsId){
		$ca=new DbQuery($this->m_db);
		$sql="select * from {$this->m_gfs_rs}
		where rsid=:rsid";

		$ca->prepare($sql);
		$ca->bindValue(":rsid",$rsId,false);
		$ca->exec();
		if( $ca->size() == 0 ){
			throw new PublicException("Resource '{$rsId}' not found");
		}

		return $ca->fetch();
	}

	public function loadBytes($rsId){
		$ca=new DbQuery($this->m_db);

		$sql="select a.rsname,a.rstype,b.data from gfs_rs a left join gfs_chunks b on (a.rsid=b.rsid) where a.rsid=:rsid order by b.segment asc";
		$ca->prepare($sql);
		$ca->bindValue(":rsid",$rsId,false);
		$ca->exec();

		foreach($ca->fetchAll() as $tmp){
			if( empty($r) ){
				$r = $tmp;
			}

			$r->data.= $tmp->data;
		}

		return base64_decode($r->data);
	}
	
	public function removeById($rsId){
		$ca=new DbQuery($this->m_db);
		

		$sql="delete from gfs_chunks where rsid = :rsid";
		$ca->prepare($sql);
		$ca->bindValue(":rsid",$rsId);
		$ca->exec();
		
		$sql="delete from gfs_rs where rsid = :rsid";
		$ca->prepare($sql);
		$ca->bindValue(":rsid",$rsId);
		$ca->exec();
		
	}
	
	public function removeByRel($relname,$relkey,$relvalue,$relfield='*'){
		$ca=new DbQuery($this->m_db);
		

		$sqlWhere="
		select 
			rsid
		from {$this->m_gfs_rs}
		where 
			relname=:relname 
			and relkey=:relkey 
			and relvalue=:relvalue
			and relfield = ( case when :relfield = '*' then relfield else :relfield end )
		";


		
		$sql="delete from gfs_chunks where rsid in ( {$sqlWhere} ) ";
		$ca->prepare($sql);
		$ca->bindValue(":relname",$relname,true);
		$ca->bindValue(":relkey",$relkey,true);
		$ca->bindValue(":relvalue",$relvalue,true);
		$ca->bindValue(":relfield",$relfield,true);
		$ca->exec();

		
		
		$sql="delete from gfs_rs where rsid in ( {$sqlWhere} )";
		$ca->prepare($sql);
		$ca->bindValue(":relname",$relname,true);
		$ca->bindValue(":relkey",$relkey,true);
		$ca->bindValue(":relvalue",$relvalue,true);
		$ca->bindValue(":relfield",$relfield,true);
		$ca->exec();
		
	}

}

?>
