<?php

$exports[]="Admin_UsuarioE";
class Admin_UsuarioE {

	public function ers(stdClass $p=null){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$res=new stdClass();

		$res->user_types =[
			['data'=>'architect','label'=>'architect'],
			['data'=>'admin','label'=>'admin'],
			['data'=>'guest','label'=>'guest']
		];

		return $res;
	}


	public function load(stdClass $p){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$res=new stdClass();
		$res->ers = $this->ers($p);

		$sql="
		select
			user_id,
			user_type,
			login,
			'' as password,
			user_name,
			email,
			notes

		from plat_users
		where
			plat_id=:plat_id
			and user_id=:user_id
		";
		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":user_id",$p->user_id);
		$ca->exec();

		$res = object_merge($res,$ca->fetch());



		$sql="
		select
			a.group_id as data,
			b.group_name as label

		from plat_user_groups a
		join plat_groups b on (a.plat_id=b.plat_id and a.group_id=b.group_id)

		where
			a.plat_id=:plat_id
			and a.user_id = :user_id

		order by
			b.group_name
		";

		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->bindValue(":user_id",$p->user_id);
		$ca->exec();
		$res->group_ids = $ca->fetchAll();

		return $res;
	}


	private function validatePassword($candidate,$len) {
	   $r1='/[A-Z]/';  //Uppercase
	   $r2='/[a-z]/';  //lowercase
	   $r3='/[!@#$%^&*()\-_=+{};:,<.>]/';  // whatever you mean by 'special char'
	   $r4='/[0-9]/';  //numbers

	   if(preg_match_all($r1,$candidate, $o)<1) return 'Debe tener al menos un caracter en mayusculas';

	   if(preg_match_all($r2,$candidate, $o)<1) return 'Debe tener al menos un caracter en minusculas';

	   if(preg_match_all($r3,$candidate, $o)<1) return 'Debe tener al menos un caracter especial';

	   if(preg_match_all($r4,$candidate, $o)<1) return 'Debe tener al menos un número';

	   if(strlen($candidate)< $len) return 'La longitud mínima es de '.$len.' caracteres';

	   return true;

   	}

	public function save(stdClass $p){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);
		$res=new stdClass();


		if(!empty($p->password) ){
			$s = $this->validatePassword($p->password,8);
			if( $s!==true ){
				throw new Cm\PublicException("Clave invalida, {$s}");
			}
		}
		$password = password_hash($p->password,PASSWORD_BCRYPT);




		$ca->prepareTable('plat_users');

		$ca->bindValue(':plat_id',$si->plat_id);
		$ca->bindValue(':login',$p->login);
		$ca->bindValue(':user_type',$p->user_type);

		$ca->bindValue(':user_name',$p->user_name);
		$ca->bindValue(':email',$p->email);
		$ca->bindValue(':notes',$p->notes);



		if( empty($p->user_id) ){
			$userId=App::nextval("plat_users_user_id");

			$ca->bindValue(':password',$password);
			$ca->bindValue(':user_id',$userId);

			$ca->execInsert();
		}
		else {
			$userId=$p->user_id;

			if(!empty($p->password)){
				$ca->bindValue(':password',$password);
			}

			$ca->bindWhere(':plat_id',$si->plat_id);
			$ca->bindWhere(':user_id',$userId);
			$ca->execUpdate();
		}


		$ca->table("plat_user_groups");
		$ca->delete( (object)[
			"plat_id"=>$si->plat_id,
			"user_id"=>$userId
		]);


		foreach($p->group_ids as $r){
			$ca->table("plat_user_groups");
			$ca->insert( (object)[
				"plat_id"=>$si->plat_id,
				"user_id"=>$userId,
				"group_id"=>$r->data
			]);
		}

		$res->user_id = $userId;
		return $res;
	}


	public function acGroups(stdClass $p,$staticFilter='1'){
		$si=App::session();
		$db=Cm\Database::database();
		$ca=new Cm\DbQuery($db);

		$p->filer = coalesce_blank($p->filter);

		$sqlFilter=[];
		$fields = "group_name";
		$sqlFilter[] = $ca->sqlFieldsFilters($fields, $p->filter);
		$sqlFilter=implode(" and ",$sqlFilter);


		$sql="
		select
			group_id as data,
			group_name as label
		from plat_groups
		where
			plat_id=:plat_id
			and {$sqlFilter}
			and {$staticFilter}

		order by
			group_name
		";

		$ca->prepare($sql);
		$ca->bindValue(":plat_id",$si->plat_id);
		$ca->exec();
		$res=$ca->fetchAll();

		return $res;
	}
}

?>
