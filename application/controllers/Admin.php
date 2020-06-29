<?php

class Admin extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('M_admin','admin');
	}
	public function index(){
		$data['guru'] = $this->db->get('tb_guru')->result();
		$data['pengajuan'] = $this->db->get('tb_pengajuan')->result();
		$data['lab'] = $this->db->get('tb_lab')->result();

		$data['title'] = 'Dashboard';
		$this->load->view('admin/templet/header',$data);
		$this->load->view('admin/templet/sidebar');
		$this->load->view('admin/dashboard',$data);
		$this->load->view('admin/templet/footer');
	}
	public function data_guru(){
		$data['kode'] = $this->admin->kode_guru();
		$data['guru'] = $this->admin->tampil_data_guru('tb_guru');
		$this->load->view('admin/templet/header');
		$this->load->view('admin/templet/sidebar');
		$this->load->view('admin/data_guru',$data);
		$this->load->view('admin/templet/footer');
	}
	public function tambah_data_guru(){
		$pesan 		= array();
		$kode		= $this->input->post('kode_guru');
		$nama		= $this->input->post('nama_guru');
		$email		= $this->input->post('email');
		$nohp		= $this->input->post('no_hp');
		$a_aktif    = $this->input->post('apakah_aktif');
		$pass		= sha1($this->input->post('password'));

		$config['upload_path'] 	 = './upload/foto_guru';
		$config['allowed_types'] = 'jpg|png|gift|jpeg';
		$config['encrypt_name']	 = true;
		$this->load->library('upload', $config);
		if(!$this->upload->do_upload('foto')){
			$foto = $this->upload->display_error();
		}else{
			$foto = $this->upload->data('file_name');
		}

		$data = [
			'nama_guru'		 => $nama,
			'kode_guru'		 => $kode,
			'email'			 => $email,
			'apakah_aktif' 	 => $a_aktif,
			'no_hp'			 => $nohp,
			'password'		 => $pass,
			'foto'			 => $foto
		];

		$insert = $this->admin->tambah_data_guru('tb_guru',$data);
		if($insert){
			$pesan['insert'] = true;
		}else{
			$pesan['insert'] = false;
		}

		echo json_encode($pesan);
	}
	public function hapus_data_guru($id,$foto){
		$pesan = array();
		$where = ['id' =>  $id];
		$delete = $this->admin->hapus_data_guru($where,'tb_guru');

		if($delete){
			$pesan['delete'] = true;
			unlink(FCPATH. 'upload/foto_guru/' . $foto);
		}else{
			$pesan['delete'] = false;
		}
		echo json_encode($pesan);
	}
	public function tampil_dataeditguru($id){
		$result = array();
		$where = ['id' => $id];

		$query = $this->admin->tampil_dataeditguru($where, 'tb_guru');

		foreach ($query as $isi) {
			$result['id']		 = $isi->id;
			$result['kode_guru'] = $isi->kode_guru;
			$result['nama_guru'] = $isi->nama_guru;
			$result['apakah_aktif'] = $isi->apakah_aktif;
			$result['email']	 = $isi->email;
			$result['foto']      = $isi->foto;
			$result['password']	 = $isi->password;
			$result['no_hp']	 = $isi->no_hp;
		}

		echo json_encode($result);
	}
	public function ubah_data_guru(){
		$pesan 		= array();
		$id 		= $this->input->post('id');
		$foto_lama  = $this->input->post('foto_lama');
		$kode		= $this->input->post('kode_guru');
		$nama		= $this->input->post('nama_guru');
		$aktif_lama = $this->input->post('aktif_lama');
		$email		= $this->input->post('email');
		$a_aktif    = $this->input->post('apakah_aktif');
		if($a_aktif == ""){
			$a_aktif = $aktif_lama;
		}else{
			$a_aktif    = $this->input->post('apakah_aktif');
		}
		$nohp		= $this->input->post('no_hp');
		$pass_i		= $this->input->post('password');

		$cek_pass = $this->db->get_where('tb_guru', ['id' => $id])->result();

		foreach ($cek_pass as $pass) {
			if($pass_i == $pass->password){
				$pass = $this->input->post('password');
			}else{
				$pass = sha1($this->input->post('password'));
			}
		}
		

		$config['upload_path'] 	 = './upload/foto_guru';
		$config['allowed_types'] = 'jpg|png|gift|jpeg';
		$config['encrypt_name']	 = true;
		$this->load->library('upload', $config);
		if(!$this->upload->do_upload('foto')){
			$foto = $foto_lama;
		}else{
			$foto = $this->upload->data('file_name');
			unlink(FCPATH . 'upload/foto_guru/' . $foto_lama);
		}

		$where = ['id' => $id];

		$data = [
			'nama_guru' => $nama,
			'kode_guru' => $kode,
			'email'		=> $email,
			'no_hp'		=> $nohp,
			'apakah_aktif' => $a_aktif,
			'password'	=> $pass,
			'foto'		=> $foto
		];

		$edit  = $this->admin->edit_data_guru($data, $where, 'tb_guru');

		if($edit){
			$pesan['edit'] = true;
		}else{
			$pesan['edit'] = false;
		}

		echo json_encode($pesan);

	}
	public function data_lab(){
		$data['lab'] = $this->admin->tampil_data_lab('tb_lab');
		$data['kode'] = $this->admin->kode_lab();
		$this->load->view('admin/templet/header');
		$this->load->view('admin/templet/sidebar');
		$this->load->view('admin/data_lab',$data);
		$this->load->view('admin/templet/footer');
	}
	public function tambah_data_lab(){
		$pesan  			= array();
		$kode_lab			= $this->input->post('kode_lab');
		$nama_lab			= $this->input->post('nama_lab');
		$fasilitas	= $this->input->post('fasilitas');
		$a_aktif    = $this->input->post('apakah_aktif');
		$keterangan			= $this->input->post('keterangan');

		$config['upload_path'] 		= './upload/foto_lab';
		$config['allowed_types']	= 'jpg|png|gift|jpeg';
		$config['encrypt_name']		= true;

		$this->load->library('upload' , $config);

		if(!$this->upload->do_upload('foto')){
			$foto = $this->upload->display_error();
		}else{
			$foto = $this->upload->data('file_name');
		}

		$data = array(
			'nama_lab' 				=>  $nama_lab,
			'kode_lab'				=>  $kode_lab,
			'fasilitas'				=>  $fasilitas,
			'apakah_aktif'			=>  $a_aktif,
			'keterangan'			=>  $keterangan,
			'foto'					=>  $foto
		);

		$insert = $this->admin->tambah_data_lab($data, 'tb_lab');

		if($insert){
			$pesan['insert']	= true;
		}else{
			$pesan['insert']	= false;
		}

		echo json_encode($pesan);
	}
	public function hapus_data_lab($id, $foto){
		$where = ['id' => $id];
		$pesan = array();

		$delete = $this->admin->hapus_data_lab('tb_lab', $where);

		if($delete){
			$pesan['delete'] = true;
			unlink(FCPATH . 'upload/foto_lab/' . $foto);
		}else{
			$pesan['delete'] = false;
		}

		echo json_encode($pesan);
	}
	public function tampil_data_edit_lab($id){
		$where  = ['id' => $id];
		$result = array();

		$query  = $this->admin->tampil_data_edit_lab('tb_lab', $where);

		foreach ($query as $isi) {
			$result['id']						=  $isi->id;
			$result['kode_lab']    		 		=  $isi->kode_lab;
			$result['apakah_aktif']				=  $isi->apakah_aktif;
			$result['nama_lab']					=  $isi->nama_lab;
			$result['fasilitas']		 		=  $isi->fasilitas;
			$result['keterangan']				=  $isi->keterangan;
			$result['foto']						=  $isi->foto;
		}

		echo json_encode($result);
	}
	public function edit_data_lab(){
		$pesan  			= array();
		$id 				= $this->input->post('id');
		$foto_lama 			= $this->input->post('foto_lama');
		$kode_lab			= $this->input->post('kode_lab');
		$nama_lab			= $this->input->post('nama_lab');
		$aktif_lama  		= $this->input->post('aktif_lama');
		$a_aktif    		= $this->input->post('apakah_aktif');
		if($a_aktif == ""){
			$a_aktif = $aktif_lama;
		}else{
			$a_aktif    		= $this->input->post('apakah_aktif');
		}
		$fasilitas			= $this->input->post('fasilitas');
		$keterangan			= $this->input->post('keterangan');

		$config['upload_path'] 		= './upload/foto_lab';
		$config['allowed_types']	= 'jpg|png|gift|jpeg';
		$config['encrypt_name']		= true;

		$this->load->library('upload' , $config);

		if(!$this->upload->do_upload('foto')){
			$foto = $foto_lama;
		}else{
			$foto = $this->upload->data('file_name');
			unlink(FCPATH . 'upload/foto_lab/' . $foto_lama);
		}

		$where = ['id' => $id];

		$data = array(
			'nama_lab' 				=>  $nama_lab,
			'kode_lab'				=>  $kode_lab,
			'fasilitas'				=>  $fasilitas,
			'apakah_aktif'			=>  $a_aktif,
			'keterangan'			=>  $keterangan,
			'foto'					=>  $foto
		);

		$query = $this->admin->edit_data_lab($data, $where, 'tb_lab');
		if($query){
			$pesan['edit'] = true;
		}else{
			$pesan['edit'] = false;
		}
		echo json_encode($pesan);
	}
	public function request_lab(){
		$data['pengajuan'] = $this->admin->tampil_data_pengajuan('tb_pengajuan',['approve' => 'tidak']);
		$data['title'] = 'Pengajuan Lab';
		$this->load->view('admin/templet/header',$data);
		$this->load->view('admin/templet/sidebar');
		$this->load->view('admin/pengajuan_lab',$data);
		$this->load->view('admin/templet/footer');
	}
	public function request_lab_approve(){
		$data['pengajuan'] = $this->admin->tampil_data_pengajuan('tb_pengajuan',['approve' => 'setuju']);
		$data['title'] = 'Pengajuan Lab';
		$this->load->view('admin/templet/header',$data);
		$this->load->view('admin/templet/sidebar');
		$this->load->view('admin/pengajuan_lab_approve',$data);
		$this->load->view('admin/templet/footer');
	}
	public function lihat_detail_pengajuan($id){
		$where = ['id' => $id];
		$data['pengajuan'] = $this->admin->tampil_detil_pengajuan('tb_pengajuan',$where);

		$data['title'] = 'Detail Pengajuan Lab';
		$this->load->view('admin/templet/header',$data);
		$this->load->view('admin/templet/sidebar');
		$this->load->view('admin/detail_pengajuan',$data);
		$this->load->view('admin/templet/footer');
	}
	public function report_pdf_pengajuan($id){
		$where = ['id' => $id];
		$result = array();
		$data= $this->admin->tampil_detil_pengajuan('tb_pengajuan',$where);

		foreach ($data as $dt) {
			$result['nama_guru'] = $dt->nama_guru;
			$result['kode_lab'] = $dt->kode_lab;
			$result['kode_guru'] = $dt->kode_guru;
			$result['kode_pengajuan'] = $dt->kode_pengajuan;
			$result['tanggal_pengajuan'] = $dt->tanggal_pengajuan;
			$result['tanggal_pemakaian'] = $dt->tanggal_pemakaian;
			$result['batas_pemakaian'] = $dt->batas_pemakaian;
			$result['nohp_guru'] = $dt->nohp_guru;
			$result['foto_guru'] = $dt->foto_guru;
			$result['keterangan'] = $dt->keterangan;
		}

		$this->load->library('pdf');
		$this->pdf->filename = 'pengajuan_lab.pdf';
		$this->pdf->load_view('admin/report_pdf_pengajuan', $result);
	}
	public function hapus_datapengajuan($id){
		$id = ['id' => $id];
		$pesan = array();
		$delete = $this->admin->hapus_datapengajuan('tb_pengajuan',$id);

		if($delete){
			$pesan['delete'] = true;
		}else{
			$pesan['delete'] = false;
		}

		echo json_encode($pesan);
	}
	public function ubah_datapengajuan($id){
		$where = ['id' => $id];
		$data = $this->admin->ubah_datapengajuan($where, 'tb_pengajuan');

		$data['title'] = 'Uabah Data Pengajuan';
		$this->load->view('admin/templet/header',$data);
		$this->load->view('admin/templet/sidebar');
		$this->load->view('admin/ubah_datapengajuan',$data);
		$this->load->view('admin/templet/footer');
	}
	public function edit_datapengajuan(){
		$kp =$this->input->post('kode_pengajuan');
		$kl =$this->input->post('kode_lab');
		$kg =$this->input->post('kode_guru');
		$ng =$this->input->post('nama_guru');
		$tp =$this->input->post('tanggal_pengajuan');
		$tpem =$this->input->post('tanggal_pemakaian');
		$bp =$this->input->post('batas_pemakaian');
		$nohp =$this->input->post('no_hp');
		$ke =$this->input->post('keterangan');

		$id = $this->input->post('id');

		$where = ['id' => $id];

		$where_riwayat_pengajuan = ['kode_pengajuan' => $kp];

		$data = [
			'kode_pengajuan'     => $kp,
			'kode_lab'     		 => $kl,
			'kode_guru'     	 => $kg,
			'nama_guru'     	 => $ng,
			'tanggal_pengajuan'	 => $tp,
			'tanggal_pemakaian'  => $tpem,
			'batas_pemakaian' 	 => $bp,
			'nohp_guru'	 		 => $nohp,
			'keterangan' 		 => $ke
		];


		$edit = $this->admin->edit_datapengajuan($data, $where, 'tb_pengajuan');
		$this->admin->edit_riwayat_pengajuan($data, $where_riwayat_pengajuan, 'tb_riwayatpengajuan');

		redirect('admin/request_lab');

		
	}
	public function riwayat_pengajuan(){
		$data['riwayat'] = $this->admin->tampil_riwayatpengajuan('tb_riwayatpengajuan');
		$data['title'] = 'Riwayat Pengajuan Lab';
		$this->load->view('admin/templet/header',$data);
		$this->load->view('admin/templet/sidebar');
		$this->load->view('admin/riwayat_pengajuan',$data);
		$this->load->view('admin/templet/footer');
	}
	public function detail_riwayat_pengajuanlab($id){
		$where = ['kode_pengajuan' => $id];
		$result = array();
		$query = $this->admin->detail_riwayat_pengajuanlab('tb_riwayatpengajuan', $where);

		foreach ($query as $isi) {
			$result['kode_pengajuan'] = $isi->kode_pengajuan;
			$result['kode_lab']		 = $isi->kode_lab;
			$result['kode_guru'] = $isi->kode_guru;
			$result['nama_guru'] = $isi->nama_guru;
			$result['tanggal_pengajuan'] = $isi->tanggal_pengajuan;
			$result['tanggal_pemakaian'] = $isi->tanggal_pemakaian;
			$result['batas_pemakaian'] = $isi->batas_pemakaian;
			$result['nohp_guru'] = $isi->nohp_guru;
			$result['foto_guru'] = $isi->foto_guru;
			$result['keterangan'] = $isi->keterangan;
		}

		echo json_encode($result);
	}
	public function hapus_riwayat_pengajuan($id){
		$where = ['id' => $id];
		$pesan = array();
		$delete = $this->admin->hapus_riwayat_pengajuan($where, 'tb_riwayatpengajuan');

		if($delete){
			$pesan['delete'] = true;
		}else{
			$pesan['delete'] = false;
		}

		echo json_encode($pesan);
	}
	public function data_admin(){
		$data['admin'] = $this->admin->tampil_admin('tb_user');
		$data['kode'] = $this->admin->kode_admin();
		$data['title'] = 'Data Admin';
		$this->load->view('admin/templet/header',$data);
		$this->load->view('admin/templet/sidebar');
		$this->load->view('admin/data_admin',$data);
		$this->load->view('admin/templet/footer');
	}
	public function tambah_admin(){
		$pesan = [];
		$kode_user 			= $this->input->post('kode_user');
		$nama 				= $this->input->post('nama');
		$username 			= $this->input->post('username');
		$password 			= sha1($this->input->post('password'));
		$password_text 		= $this->input->post('password');
		$email 				= $this->input->post('email');
		$no_hp 				= $this->input->post('no_hp');
		$status_aktif 		= $this->input->post('status_aktif');

		$data = [
			'kode_user' 	=> $kode_user,
			'nama' 			=> $nama,
			'username' 		=> $username,
			'password' 		=> $password,
			'text_password' => $password_text,
			'email' 		=> $email,
			'no_hp' 		=> $no_hp,
			'status_aktif' 	=> $status_aktif,
		];

		$tambah = $this->admin->tambah_admin('tb_user',$data);
		if($tambah){
			$pesan['tambah'] = true;
		}else{
			$pesan['tambah'] = false;
		}

		echo json_encode($pesan);
	}
	public function hapus_admin()
	{
		$where  = ['kode_user' => $this->input->post('id')];
		$delete = $this->admin->hapus_admin('tb_user', $where);
		$pesan  = [];
		if($delete){
			$pesan['delete'] = true;
		}else{
			$pesan['delete'] = false;
		}
		echo json_encode($pesan);
	}
	public function	tampil_data_admin(){
		$where  = ['kode_user' => $this->input->post('id')];
		$data   = $this->admin->tampil_admin('tb_user',$where);
		echo json_encode($data);
	}
	public function edit_data_admin(){
		$pesan = [];
		$kode_user 			= $this->input->post('kode_user');
		$nama 				= $this->input->post('nama');
		$username 			= $this->input->post('username');
		$password 			= sha1($this->input->post('password'));
		$password_text 		= $this->input->post('password');
		$email 				= $this->input->post('email');
		$no_hp 				= $this->input->post('no_hp');
		$status_aktif 		= $this->input->post('status_aktif');

		$where = ['kode_user' => $this->input->post('kode_user_lama')];

		$data = [
			'kode_user' 	=> $kode_user,
			'nama' 			=> $nama,
			'username' 		=> $username,
			'password' 		=> $password,
			'text_password' => $password_text,
			'email' 		=> $email,
			'no_hp' 		=> $no_hp,
			'status_aktif' 	=> $status_aktif,
		];

		$edit = $this->admin->edit_admin('tb_user',$data, $where);
		if($edit){
			$pesan['edit'] = true;
		}else{
			$pesan['edit'] = false;
		}

		echo json_encode($pesan);
	}
	public function approve_pengajuan(){
		$pesan = [];
		$where = ['kode_pengajuan' => $this->input->post('id')];
		$data = ['approve' => 'setuju'];
		$approve = $this->admin->approve_pengajuan('tb_pengajuan',$data, $where);
		if($approve){
			$this->admin->approve_pengajuan('tb_riwayatpengajuan',$data, $where);
			$pesan['approve'] = true;
		}else{
			$pesan['approve'] = false;
		}

		echo json_encode($pesan);
	}
}
