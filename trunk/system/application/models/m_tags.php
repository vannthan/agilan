<?php
/*
CREATE TABLE `tags` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`tag` VARCHAR( 64 ) NOT NULL ,
`object` VARCHAR( 64 ) NOT NULL ,
`object_id` INT NOT NULL ,
`user_id` INT NOT NULL ,
`created` TIMESTAMP NOT NULL
) ENGINE = MYISAM ;


CREATE TABLE `tag_follows` (
`user_id` INT NOT NULL ,
`tag` VARCHAR( 128 ) NOT NULL ,
PRIMARY KEY ( `user_id` , `tag_id` )
) ENGINE = MYISAM ;
*/


class m_tags extends Model{
	
	function m_tags(){
		parent::Model();
	}


	function search_tags($input){
		$data = array();
		$term = xss_clean(substr($input,0,255));
		$this->db->select('user_id');
		$this->db->like('tag', $term);
		$this->db->like('object', 'users');
		$Q = $this->db->get("tags");
		if ($Q->num_rows() > 0){
			foreach ($Q->result() as $row){
				$data[$row->user_id] = true;
			}
		}
		$Q->free_result();
		return $data;	
	}

	
	function list_tags(){
		$userid = $_SESSION['userid'];
		$data = array();
		$this->db->select("tag");
		$this->db->order_by('tag','asc');
		$this->db->where("user_id",$userid);
		$Q = $this->db->get("tag_follows");
		if ($Q->num_rows() > 0){
			foreach ($Q->result() as $row){
				$data[] = $row->tag;
			}
		}
		

		$Q->free_result();		
		return $data;			

	}


	function list_all_tags(){
		$data = array();
		$this->db->select("tag");
		$this->db->order_by('tag','asc');
		$Q = $this->db->get("tag_follows");
		if ($Q->num_rows() > 0){
			foreach ($Q->result() as $row){
				$data[$row->tag] = $row->tag;
			}
		}
		

		$Q->free_result();		
		return $data;			

	}



	function check_tag_exists($tag){
		$this->db->select('id');
		$this->db->like('tag', $tag);
		$this->db->limit(1);
		$Q = $this->db->get("tags");
		if ($Q->num_rows() > 0){
			$row = $Q->row();
			return $row->id;
		}else{
			return 0;
		}
		
	}



	function follow_tag($tag){	
		$userid = $_SESSION['userid'];
		$data = array(
				'tag' => xss_clean($tag),
				'user_id' => $userid,
			);
			
		$this->db->insert("tag_follows",$data);				
	}
	
	function unfollow_tag($tag){
		$userid = $_SESSION['userid'];
		$this->db->limit(1);
		$this->db->where('tag', $tag);
		$this->db->where('user_id', $userid);
		$this->db->delete('tag_follows');	
	
	}
	

	function list_objects($tag){
		$data = array();
		
		$this->db->like("tag",$tag);
		$Q = $this->db->get("tags");
		if ($Q->num_rows() > 0){
			foreach ($Q->result() as $row){
				$data[] = $row;
			}
		}
		
		$Q->free_result();	
		

		return $data;				
	}

	function list_tag_users(){
		$data = array();
		$this->db->select("tag,object_id");
		$this->db->where("object","users");
		$Q = $this->db->get("tags");
		if ($Q->num_rows() > 0){
			foreach ($Q->result() as $row){
				$data[$row->object_id][] = $row->tag;
			}
		}
		
		$Q->free_result();		
		return $data;		
	
	}

	function list_tag_one_user($userid){
		$data = array();
		$this->db->select("tag");
		$this->db->where("object","users");
		$this->db->where("object_id",$userid);
		$Q = $this->db->get("tags");
		if ($Q->num_rows() > 0){
			foreach ($Q->result() as $row){
				$data[] = $row->tag;
			}
		}
		
		$Q->free_result();		
		return $data;		
	
	}
	
	function list_tag_objects($object){
		$userid = $_SESSION['userid'];
		$data = array();
		$this->db->select("tag,object_id");
		$this->db->where("user_id",$userid);
		$this->db->where("object",$object);
		$Q = $this->db->get("tags");
		if ($Q->num_rows() > 0){
			foreach ($Q->result() as $row){
				$data[$row->object_id][] = $row->tag;
			}
		}
		

		$Q->free_result();		
		return $data;		
	
	}

	function list_tag_objects_single($id,$object){
		//$userid = $_SESSION['userid'];
		//$this->db->where("user_id",$userid);
		$data = array();
		$this->db->select("tag,object_id");
		$this->db->where("object",$object);
		$this->db->where("object_id",$id);
		$Q = $this->db->get("tags");
		if ($Q->num_rows() > 0){
			foreach ($Q->result() as $row){
				$data[$row->object_id][] = $row->tag;
			}
		}
		

		$Q->free_result();		
		return $data;		
	
	}
	
	function delete_tag($id){
		$this->db->limit(1);
		$this->db->where('id', $id);
		$this->db->delete('tags');	
	
	}
	
	function add_tags($tags,$object,$object_id){
		$tags_array = explode(",",$tags);
		if (!isset($_SESSION['userid'])){
			$userid = $object_id;
		}else{
			$userid = $_SESSION['userid'];
		}
		$now = date("Y-m-d h:i:s");
		
		
		//first, remove all tags associated with object and object_id
		$this->db->where('object', $object);
		$this->db->where('object_id',$object_id);
		$this->db->where('user_id',$userid);
		$this->db->delete('tags');
		
		
		//now add the new stuff
		foreach ($tags_array as $tag){
			
			$data = array(
				'tag' => xss_clean($tag),
				'object' => $object,
				'object_id' => $object_id,
				'user_id' => $userid,
				'created' => $now
			);
			
			$this->db->insert("tags",$data);			
		
		}
	
	
	}

	
}//end class

?>