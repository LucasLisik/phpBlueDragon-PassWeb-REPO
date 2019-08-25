<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
https://stackoverflow.com/a/30905277/3946924

X - Usuwanie haseł po pewnym czasie - Ustawione na 1 godzinę
X - Instalator
X - About
X - Components
X - Poprawki w języku oraz plikach
X - Poprawki w wyglądzie na mniejsze ekrany
Poprawki w wyświetlaniu podkategorii

*/

class Systemid extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('System_model');
        $this->load->library('form_validation');
        $this->output->enable_profiler(false);
        
        $this->lang->load('system', $this->config->item('language'));
        
        $this->System_model->DeleteOldPasswords();
    }
        
    public function index($CategoryId='',$Action='',$SubAction="")
    {        
        if($_SESSION['user_id'] == "")
        {
            $ResultDB = $this->System_model->GetPasswordsList();
        
            $TableData = null;
            
            foreach($ResultDB->result() as $row)
            {
                $TableData[$row->passwordlist_id] = $row->passwordlist_name;
            }
            
            //
            // Login form
            //
            if($this->input->post('formlogin') == 'yes')
    		{
    			$this->form_validation->set_rules('passwordlist_name', ''.$this->lang->line('a1078').'', 'required');
    			$this->form_validation->set_rules('passwordlist_password', ''.$this->lang->line('a1079').'', 'required');
                
                $SystemLang['Fpassword_list_name'] = $this->input->post('passwordlist_name');
                
    			if($this->form_validation->run() != FALSE)
    			{
    				$TableUser = $this->System_model->CheckUser($this->input->post('passwordlist_name'),$this->input->post('passwordlist_password'));

    				if($TableUser['IsAuth'] == 'no')
                    {
                        $SystemLang['bad_data'] = true;
                    }
                    
                    if($TableUser['IsAuth'] == 'yes')
                    {
                        $_SESSION['user_id'] = $TableUser['UserId'];
                        $_SESSION['user_password'] = $TableUser['UserPassword'];
                        $_SESSION['user_iv'] = $TableUser['UserIV'];
                        
                        //echo 'Yes Yes Yes!';
                        redirect();
                    }
                }
            }
            
            //
            // New repo form
            //
            if($this->input->post('formnewrepo') == 'yes')
    		{
    			$this->form_validation->set_rules('passwordlist_name', ''.$this->lang->line('a1082').'', 'required');
    			$this->form_validation->set_rules('pass_password', ''.$this->lang->line('a0948').'', 'required');
                $this->form_validation->set_rules('pass_password2', ''.$this->lang->line('a1081').'', 'required|matches[pass_password]');
    
    			if($this->form_validation->run() != FALSE)
    			{
                    $this->System_model->CreateNewRepo($this->input->post('passwordlist_name'),$this->input->post('pass_password'));
                    $SystemLang['new_repo_created'] = true;
                }
            }
            
            $SystemLang['HowManyRecords'] = count($TableData);
            
            $SystemLang['Title'] = ''.$this->lang->line('a0862').'';
            $SystemLang['Content'] = ''.$this->lang->line('a0863').'';
            
            $this->load->view('head',$SystemLang);
            
            $this->load->view('login', $SystemLang);
            
            $this->load->view('foot');
        }
        else
        {
            if($Action == 'deletecategory')
            {
                $this->System_model->DeleteCategory($SubAction);
                
                $SystemLang['CategoryDeleted'] = true;
            }
            
            if($Action == 'deletepassword')
            {
                $this->System_model->DeletePassword($SubAction);
                
                $SystemLang['PasswordDeleted'] = true;
            }
            
            //
            // Password main list
            //
            $SystemLang['Title'] = ''.$this->lang->line('a1084').'';
            $SystemLang['Content'] = ''.$this->lang->line('a1085').'';
            
            $this->load->view('head',$SystemLang);
                        
            //
            // Categories
            //
            
            $ResultDB = $this->System_model->SelectCategoryList($_SESSION['user_id']);
        
            $TableData = null;
            $TableDataSub = null;
            //$TableDataKey = null;
            
            foreach($ResultDB->result() as $row)
            {
                //$row->category_password_sub == "" OR 
                if($row->category_password_sub == "0")
                {
                    $TableData[$row->category_id] = $row->category_name;
                    //$TableDataKey[$row->category_id] = $row->category_password_iv;
                }
            }
            
            foreach($ResultDB->result() as $row)
            {
                //$row->category_password_sub != "" OR 
                if($row->category_password_sub != "0")
                {
                    $TableDataSub[] = array('category_id' => $row->category_password_sub, 'category_sub_id' => $row->category_id, 'category_name' => $row->category_name);
                    //$TableDataKey[$row->category_id] = $row->category_password_iv;
                }
            }
            
            //echo '<pre>';
            //print_r($TableDataSub);
            //echo '</pre>';
            
            //
            // Passwords
            //
            
            if($CategoryId == "" OR $CategoryId == "0")
            {
                if($SystemLang['CategoryDeleted'] == true)
                {
                    $CategortContent = '<div class="alert alert-success">'.$this->lang->line('a1107').'</div><br /><br />';
                }
                $CategortContent .= '<div class="alert alert-info">'.$this->lang->line('a1108').'</div>';
            }
            else
            {
                $SystemLang['CategoryIdIs'] = $CategoryId;

                $SystemLang['QueryResultTable'] = $this->System_model->SelectOneCategoryName($CategoryId);
                
                $ResultDBPass = $this->System_model->SelectPasswordList($_SESSION['user_id'],$CategoryId);
        
                $TableDataPass = null;
                
                $CategortContent = '<div class="container">';
                
                $CategortContent .= '<a href="'.base_url('addnewpassword/'.$CategoryId).'" class="btn btn-info btn-block"><span class="fa fa-paperclip"></span> '.$this->lang->line('a1109').'</a><br />
                
                <div id="infocopied" style="display: none;" class="alert alert-success">'.$this->lang->line('a1110').'</div>
                ';
                
                $CategortContent .= '<div id="infopassword" style="display: none; font-weight: bold; padding-top: 5px; padding-bottom: 5px;" class="alert alert-success text-center"></div>';
                
                if($SystemLang['PasswordDeleted'] == true)
                {
                    $CategortContent .= '<div class="alert alert-success" id="passwordWasDeleted">'.$this->lang->line('a1111').'</div>';
                }
                
                $CategortContent .= '<div class="row" style="background-color: #2BABD2; color: #ffffff; margin: 5px;">
                                            <div class="col-md-3" style="margin-top: 5px; margin-bottom: 5px;">'.$this->lang->line('a1112').'</div>
                                            <div class="col-md-2" style="margin-top: 5px; margin-bottom: 5px;">'.$this->lang->line('a1113').'</div>
                                            <div class="col-md-1" style="margin-top: 5px; margin-bottom: 5px;">'.$this->lang->line('a1114').'</div>
                                            <div class="col-md-5" style="margin-top: 5px; margin-bottom: 5px;">'.$this->lang->line('a1115').'</div>
                                            <div class="col-md-1" style="margin-top: 5px; margin-bottom: 5px;">'.$this->lang->line('a1116').'</div>
                                         </div>';
                               
                $IsSetPaswordThisList = false;
                          
                foreach($ResultDBPass->result() as $row)
                {
                    $IsSetPaswordThisList = true;

                    $CategortContent .= '<div class="row" style="border-top: solid 1px #2BABD2; margin: 5px;">
                                            <div class="col-md-3" style="margin-top: 5px; margin-bottom: 5px;">'.$this->System_model->decrypt($row->pass_title,$_SESSION['user_iv'],$_SESSION['user_password']).'</div>
                                            <div class="col-md-2" style="margin-top: 5px; margin-bottom: 5px;" id="rowuser'.$row->pass_id.'">'.$this->System_model->decrypt($row->pass_user,$_SESSION['user_iv'],$_SESSION['user_password']).'</div>
                                            <div class="col-md-1" style="margin-top: 5px; margin-bottom: 5px;" id="rowpassword'.$row->pass_id.'">***</div>
                                            <div class="col-md-5" style="margin-top: 5px; margin-bottom: 5px;" id="rowurl'.$row->pass_id.'">'.$this->System_model->decrypt($row->pass_url,$_SESSION['user_iv'],$_SESSION['user_password']).'</div>
                                            <div class="col-md-1" style="margin-top: 5px; margin-bottom: 5px;">
                                                <div class="dropdown">
                                                  <button type="button" class="btn btn-info dropdown-toggle btn-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    '.$this->lang->line('a1117').'
                                                  </button>
                                                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item" href="'.base_url('editpassword/'.$CategoryId.'/'.$row->pass_id).'"><span class="fa fa-edit"></span> '.$this->lang->line('a1088').'</a>
                                                    <a class="dropdown-item" href="JavaScript:DeteleInfo(\''.base_url().'deletepassword/'.$CategoryId.'/'.$row->pass_id.'\',\''.$this->lang->line('a1047').'\');"><span class="fa fa-eraser"></span> '.$this->lang->line('a1089').'</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item" onclick="copyToClipboard(\'#rowuser'.$row->pass_id.'\');"><span class="fa fa-files-o"></span> '.$this->lang->line('a1118').'</a>
                                                    <a class="dropdown-item" onclick="openPasswordToView(\''.$row->pass_id.'\');"><span class="fa fa-files-o"></span> '.$this->lang->line('a1119').'</a>
                                                    <a class="dropdown-item" onclick="openNewWindow(\'#rowurl'.$row->pass_id.'\');"><span class="fa fa-window-restore"></span> '.$this->lang->line('a1120').'</a>
                                                  </div>
                                                </div>
                                            </div>
                                         </div>';
                }
                
                if($IsSetPaswordThisList == false)
                {
                    $CategortContent .= '<div class="row" style="border-top: solid 1px #2BABD2; margin: 5px;">
                        <div class="col-md-12" style="margin-top: 5px; margin-bottom: 5px; text-align: center;">'.$this->lang->line('a1121').'</div>
                    </div>';
                }
                
                $CategortContent .= '</div>';
            }
            
            $SystemLang['ContentPage'] = '
            
            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                    <a href="'.base_url('addcategory/').'" class="btn btn-info btn-block"><span class="fa fa-link"></span> '.$this->lang->line('a1122').'</a><br />';
                    
                    if($TableData != "")
                    {
                        $SystemLang['ContentPage'] .= '<a href="'.base_url('addsubcategory/').'" class="btn btn-info btn-block"><span class="fa fa-link"></span> '.$this->lang->line('a1123').'</a><br />';
                    }
                    
                    $SystemLang['ContentPage'] .= '<strong>'.$this->lang->line('a1124').'</strong>
                    <ul style="font-size: 18px;">';
                    
                    if($TableData != "")
                    {
                        foreach ($TableData as $key => $value)
                        {
                            $SystemLang['ContentPage'] .= '<li class="fa fa-arrow-circle-right" style="display: block;">
                            <a href="'.base_url('editcategory/'.$key).'"><span class="fa fa-edit" style="color: #2BABD2;" title="'.$this->lang->line('a1188').'"></span></a> 
                            <a href="JavaScript:DeteleInfo(\''.base_url().'deletecategory/'.$key.'\',\''.$this->lang->line('a1047').'\');"><span class="fa fa-eraser" style="color: #C9302C;" title="'.$this->lang->line('a1189').'"></span></a> 
                            <a href="'.base_url('index/'.$key).'">'.$this->System_model->decrypt($value,$_SESSION['user_iv'],$_SESSION['user_password']).'</a></li>';
                            
                            //echo '<pre>';
                            //print_r($TableDataSub);
                            //echo '</pre>';
                            
                            //$TableDataSub[$row->category_password_sub] = array($row->category_id => $row->category_name);
                            
                            //if(is_array($TableDataSub[$key]))
                            //{
                                
                                //$TableDataSub[] = array('category_id' => $row->category_id, 'category_sub_id' => $row->category_password_sub, 'category_name' => $row->category_name);
                                
                                $SpecialSubCategoryTable = false;
                                //echo '<pre>'; print_r($TableDataSub); echo '</pre>';
                                
                                for($i=0;$i<count($TableDataSub);$i++)
                                {
                                    if($TableDataSub[$i]['category_id'] == $key)
                                    {
                                        $SpecialSubCategoryTable .= '<li class="fa fa-arrow-circle-right" style="display: block;">
                                        <a href="'.base_url('editsubcategory/'.$TableDataSub[$i]['category_sub_id']).'"><span class="fa fa-edit" style="color: #2BABD2;" title="'.$this->lang->line('a1188').'"></span></a>
                                        <a href="JavaScript:DeteleInfo(\''.base_url().'deletecategory/'.$TableDataSub[$i]['category_sub_id'].'\',\''.$this->lang->line('a1047').'\');"><span class="fa fa-eraser" style="color: #C9302C;" title="'.$this->lang->line('a1189').'"></span></a>
                                        <a href="'.base_url('index/'.$TableDataSub[$i]['category_sub_id']).'">'.$this->System_model->decrypt($TableDataSub[$i]['category_name'],$_SESSION['user_iv'],$_SESSION['user_password']).'</a></li>';                                 
                                    }
                                }
                                
                                if($SpecialSubCategoryTable != false)
                                {
                                    $SystemLang['ContentPage'] .= '<ul>';
                                    $SystemLang['ContentPage'] .= $SpecialSubCategoryTable;
                                    $SystemLang['ContentPage'] .= '</ul>';
                                }
                                
                                /*$SystemLang['ContentPage'] .= '<ul>';
                                
                                foreach ($TableDataSub[$key] as $key => $value)
                                {
                                    //echo $value.'--';
                                    $SystemLang['ContentPage'] .= '<li class="fa fa-arrow-circle-right" style="display: block;">
                                    <a href="'.base_url('editsubcategory/'.$key).'"><span class="fa fa-edit" style="color: #2BABD2;"></span></a>
                                    <a href="JavaScript:DeteleInfo(\''.base_url().'deletecategory/'.$key.'\',\''.$this->lang->line('a1047').'\');"><span class="fa fa-eraser" style="color: #C9302C;"></span></a>
                                    <a href="'.base_url('index/'.$key).'">'.$this->System_model->decrypt($value,$_SESSION['user_iv'],$_SESSION['user_password']).'</a></li>';
                                }
                                
                                $SystemLang['ContentPage'] .= '</ul>';*/
                            //}
                        }
                    }
                    
                    $SystemLang['ContentPage'] .= '</ul>
                    </div>
                    <div class="col-md-9">'.$CategortContent.'
                    </div>
                </div>
            </div>
            ';
            
            $this->load->view('pass/show', $SystemLang);
            
            $this->load->view('foot');
        }
    }
    
    
    
    public function getpswd($PasswordId)
    {
        if($_SESSION['user_id'] == ""){redirect();exit();}
        
        //echo 'HasloJakies';
        
        $ResultDB = $this->System_model->SelectOnePasswordView($PasswordId);
               
        $PasswordIs = null;
        
        foreach($ResultDB->result() as $row)
        {
            $PasswordIs = $row->pass_password;
        }
        
        if($PasswordIs != null)
        {
            echo $this->System_model->decrypt($PasswordIs,$_SESSION['user_iv'],$_SESSION['user_password']);
        }
        else
        {
            echo ''.$this->lang->line('a1125').'';
        }
    }
    
    public function delete()
    {
        if($_SESSION['user_id'] == ""){redirect();exit();}
        
        $SystemLang['Title'] = ''.$this->lang->line('a1126').'';
        $SystemLang['Content'] = ''.$this->lang->line('a1127').'';
        
        if($this->input->post('formdelete') == 'yes')
		{
            $this->form_validation->set_rules('acceptdelete', ''.$this->lang->line('a1128').'', 'required');
            
    		if($this->form_validation->run() != FALSE)
    		{
  		        $this->System_model->DeleteAllData();
                
                $_SESSION['user_id'] = '';
                $_SESSION['user_password'] = '';
                $_SESSION['user_iv'] = '';
        
                redirect();
            }
        }
        
        $this->load->view('head',$SystemLang);
        
        $this->load->view('delete', $SystemLang);
            
        $this->load->view('foot');
    }
    
    public function error404()
    {
        redirect();
    }
    
    public function addeditpassword($Action,$CategoryId,$PasswordId='')
    {
        if($_SESSION['user_id'] == ""){redirect();exit();}
        
        $SystemLang['PassField'] = true;
        
        $SystemLang['QueryResultTable'] = $this->System_model->SelectOneCategoryName($CategoryId);
        
        $SystemLang['action'] = $Action;
        $SystemLang['PasswordId'] = $PasswordId;
        $SystemLang['CategoryId'] = $CategoryId;
        
        if($Action == 'edit')
        {
            $ResultDB = $this->System_model->SelectOnePassword($PasswordId);
                  
            foreach($ResultDB->result() as $row)
            {
                //$PasswordOwnName = $row->category_name;
                /*$SystemLang['Vpass_id'] = $row->pass_id; 	
                $SystemLang['Vpass_title'] = $row->pass_title; 	
                $SystemLang['Vpass_user'] = $row->pass_user; 	
                $SystemLang['Vpass_password'] = $row->pass_password;
                $SystemLang['Vpass_password2'] = $row->pass_password; 	 	
                $SystemLang['Vpass_url'] = $row->pass_url; 	
                $SystemLang['Vpass_note'] = $row->pass_note;*/	
                
                $SystemLang['Vpass_id'] = $row->pass_id; 	
                $SystemLang['Vpass_title'] = $this->System_model->decrypt($row->pass_title,$_SESSION['user_iv'],$_SESSION['user_password']);; 	
                $SystemLang['Vpass_user'] = $this->System_model->decrypt($row->pass_user,$_SESSION['user_iv'],$_SESSION['user_password']);; 	
                $SystemLang['Vpass_password'] = $this->System_model->decrypt($row->pass_password,$_SESSION['user_iv'],$_SESSION['user_password']);;
                $SystemLang['Vpass_password2'] = $this->System_model->decrypt($row->pass_password,$_SESSION['user_iv'],$_SESSION['user_password']);; 	 	
                $SystemLang['Vpass_url'] = $this->System_model->decrypt($row->pass_url,$_SESSION['user_iv'],$_SESSION['user_password']);; 	
                $SystemLang['Vpass_note'] = $this->System_model->decrypt($row->pass_note,$_SESSION['user_iv'],$_SESSION['user_password']);; 
                
                
            }
        }
        
        /*if($Action == 'edit' && $SystemLang['Vpass_id'] != "")
        {
            //$SystemLang['Vpass_id'] = $this->input->post('pass_id');
            $SystemLang['Vpass_title'] = $this->input->post('pass_title'); 	
            $SystemLang['Vpass_user'] = $this->input->post('pass_user');
            $SystemLang['Vpass_password'] = $this->input->post('pass_password');
            $SystemLang['Vpass_password2'] = $this->input->post('pass_password2');  	
            $SystemLang['Vpass_url'] = $this->input->post('pass_url');
            $SystemLang['Vpass_note'] = $this->input->post('pass_note'); 
        }*/
        
        if($this->input->post('formlogin') == 'yes')
		{
            $this->form_validation->set_rules('pass_title', ''.$this->lang->line('a1112').'', 'required');
            $this->form_validation->set_rules('pass_user', ''.$this->lang->line('a1113').'', 'required');
            $this->form_validation->set_rules('pass_password', ''.$this->lang->line('a1114').'', 'required');
            $this->form_validation->set_rules('pass_password2', ''.$this->lang->line('a1129').'', 'required|matches[pass_password]');
            $this->form_validation->set_rules('pass_url', ''.$this->lang->line('a1115').'', 'valid_url');
            //$this->form_validation->set_rules('pass_note', 'Notatki', 'required'); 
            
            if($Action == 'add')
            {
    			if($this->form_validation->run() != FALSE)
    			{
                    $this->System_model->CreateNewPassword($CategoryId);
                    $SystemLang['new_password_added'] = true;
                    //$SystemLang['Vpass_id'] = "";
                    $SystemLang['Vpass_title'] = ""; 	
                    $SystemLang['Vpass_user'] = "";
                    $SystemLang['Vpass_password'] = "";
                    $SystemLang['Vpass_password2'] = "";  	
                    $SystemLang['Vpass_url'] = "";
                    $SystemLang['Vpass_note'] = "";
                }
                else
                {
                    $SystemLang['Vpass_title'] = $this->input->post('pass_title'); 	
                    $SystemLang['Vpass_user'] = $this->input->post('pass_user');
                    $SystemLang['Vpass_password'] = $this->input->post('pass_password'); 	
                    $SystemLang['Vpass_password2'] = $this->input->post('pass_password2');
                    $SystemLang['Vpass_url'] = $this->input->post('pass_url');
                    $SystemLang['Vpass_note'] = $this->input->post('pass_note'); 
                }
            }
            else
            {
    			if($this->form_validation->run() != FALSE)
    			{
                    $this->System_model->EditPassword($PasswordId);
                    //$SystemLang['new_repo_created'] = true;
                    $SystemLang['password_edited'] = true;
                    $SystemLang['Vpass_title'] = $this->input->post('pass_title'); 	
                    $SystemLang['Vpass_user'] = $this->input->post('pass_user');
                    $SystemLang['Vpass_password'] = $this->input->post('pass_password'); 	
                    $SystemLang['Vpass_password2'] = $this->input->post('pass_password2');
                    $SystemLang['Vpass_url'] = $this->input->post('pass_url');
                    $SystemLang['Vpass_note'] = $this->input->post('pass_note'); 
                    //$this->input->post('category_name');
                }
                else
                {
                    $SystemLang['Vpass_title'] = $this->input->post('pass_title'); 	
                    $SystemLang['Vpass_user'] = $this->input->post('pass_user');
                    $SystemLang['Vpass_password'] = $this->input->post('pass_password'); 	
                    $SystemLang['Vpass_password2'] = $this->input->post('pass_password2');
                    $SystemLang['Vpass_url'] = $this->input->post('pass_url');
                    $SystemLang['Vpass_note'] = $this->input->post('pass_note'); 
                }
            }
        }
        
        if($Action == 'add')
        {
            $SystemLang['Title'] = ''.$this->lang->line('a1130').'';
            $SystemLang['Content'] = ''.$this->lang->line('a1131').'';
        }
        else
        {
            $SystemLang['Title'] = ''.$this->lang->line('a1132').'';
            $SystemLang['Content'] = ''.$this->lang->line('a1133').'';
        }
        
        $this->load->view('head',$SystemLang);
        
        $this->load->view('pass/addedit', $SystemLang);
        
        $this->load->view('foot');
    }
    
    public function addeditcategory($Action,$CategoryId='')
    {
        if($_SESSION['user_id'] == ""){redirect();exit();}
        
        $SystemLang['action'] = $Action;
        $SystemLang['CategoryId'] = $CategoryId;
        
        if($Action == 'edit')
        {
            $ResultDB = $this->System_model->SelectOneCategory($CategoryId);
                  
            foreach($ResultDB->result() as $row)
            {
                $CategoryOwnName = $row->category_name;
                
                $CategoryOwnName = $this->System_model->decrypt($CategoryOwnName,$_SESSION['user_iv'],$_SESSION['user_password']);
            }
        }
        
        if($Action == 'edit')
        {
            $SystemLang['Vcategory_name'] = $CategoryOwnName;
        }
        
        if($this->input->post('formlogin') == 'yes')
		{
			$this->form_validation->set_rules('category_name', ''.$this->lang->line('a1134').'', 'required');

            if($Action == 'add')
            {
    			if($this->form_validation->run() != FALSE)
    			{
                    $this->System_model->CreateNewCategory($this->input->post('category_name'));
                    $SystemLang['new_category_added'] = true;
                    $SystemLang['Vcategory_name'] = "";
                }
            }
            else
            {
    			if($this->form_validation->run() != FALSE)
    			{
                    $this->System_model->EditCategory($this->input->post('category_name'),$CategoryId);
                    //$SystemLang['new_repo_created'] = true;
                    $SystemLang['category_edited'] = true;
                    $SystemLang['Vcategory_name'] = $this->input->post('category_name');
                    //$this->input->post('category_name');
                }
            }
        }
        
        if($Action == 'add')
        {
            $SystemLang['Title'] = ''.$this->lang->line('a1135').'';
            $SystemLang['Content'] = ''.$this->lang->line('a1136').'';
        }
        else
        {
            $SystemLang['Title'] = ''.$this->lang->line('a1137').'';
            $SystemLang['Content'] = ''.$this->lang->line('a1138').'';
        }
        
        $this->load->view('head',$SystemLang);
        
        $this->load->view('category/addedit', $SystemLang);
        
        $this->load->view('foot');
    }
    
    public function addeditsubcategory($Action,$CategoryId='')
    {
        if($_SESSION['user_id'] == ""){redirect();exit();}
        
        $SystemLang['action'] = $Action;
        $SystemLang['CategoryId'] = $CategoryId;
        
        if($Action == 'edit')
        {
            $ResultDB = $this->System_model->SelectOneCategory($CategoryId);
                  
            foreach($ResultDB->result() as $row)
            {
                $CategoryOwnName = $row->category_name;
                $CategoryOwnParent = $row->category_password_sub;
                
                $CategoryOwnName = $this->System_model->decrypt($CategoryOwnName,$_SESSION['user_iv'],$_SESSION['user_password']);
            }
        }
        
        if($Action == 'edit')
        {
            $SystemLang['Vcategory_name'] = $CategoryOwnName;
            $SystemLang['Vcategory_main'] = $CategoryOwnParent;
        }
        
        if($this->input->post('formlogin') == 'yes')
		{
            $this->form_validation->set_rules('category_main', ''.$this->lang->line('a1139').'', 'required');
			$this->form_validation->set_rules('category_name', ''.$this->lang->line('a1140').'', 'required');

            if($Action == 'add')
            {
    			if($this->form_validation->run() != FALSE)
    			{
                    $this->System_model->CreateNewSubCategory($this->input->post('category_main'),$this->input->post('category_name'));
                    $SystemLang['new_category_added'] = true;
                    $SystemLang['Vcategory_name'] = "";
                    $SystemLang['Vcategory_main'] = "";
                }
                else
                {
                    $SystemLang['Vcategory_name'] = $this->input->post('category_name');
                    $SystemLang['Vcategory_main'] = $this->input->post('category_main');
                }
            }
            else
            {
    			if($this->form_validation->run() != FALSE)
    			{
                    $this->System_model->EditSubCategory($this->input->post('category_main'),$this->input->post('category_name'),$CategoryId);
                    //$SystemLang['new_repo_created'] = true;
                    $SystemLang['category_edited'] = true;
                    $SystemLang['Vcategory_name'] = $this->input->post('category_name');
                    $SystemLang['Vcategory_main'] = $this->input->post('category_main');
                    //$this->input->post('category_name');
                }
                else
                {
                    $SystemLang['Vcategory_name'] = $this->input->post('category_name');
                    $SystemLang['Vcategory_main'] = $this->input->post('category_main');
                }
            }
        }
        
        if($Action == 'add')
        {
            $SystemLang['Title'] = ''.$this->lang->line('a1141').'';
            $SystemLang['Content'] = ''.$this->lang->line('a1142').'';
        }
        else
        {
            $SystemLang['Title'] = ''.$this->lang->line('a1143').'';
            $SystemLang['Content'] = ''.$this->lang->line('a1144').'';
        }
        
        $this->load->view('head',$SystemLang);
        
        $this->load->view('category/addeditsub', $SystemLang);
        
        $this->load->view('foot');
    }
    
    public function checkselection($str)
    {
        if(
            $this->input->post('password_lenght') == 'yes' OR
            $this->input->post('upercase') == 'yes' OR
            $this->input->post('lowercase') == 'yes' OR
            $this->input->post('digits') == 'yes' OR
            $this->input->post('underline') == 'yes' OR
            $this->input->post('space') == 'yes' OR
            $this->input->post('minus') == 'yes' OR
            $this->input->post('special') == 'yes' OR
            $this->input->post('brackets')
        )
        {
            return TRUE;
        }
        else
        {
            $this->form_validation->set_message('checkselection', ''.$this->lang->line('a1099').'');
			return FALSE;
        }
    }
    
    public function generator()
    {
        if($_SESSION['user_id'] == ""){redirect();exit();}
        
        $SystemLang['Title'] = ''.$this->lang->line('a1094').'';
        $SystemLang['Content'] = ''.$this->lang->line('a1095').'';
        
        $this->load->view('head',$SystemLang);
                
        if($this->input->post('formgenerator') == 'yes')
		{
			$this->form_validation->set_rules('upercase', ''.$this->lang->line('a1096').'', 'callback_checkselection');
			$this->form_validation->set_rules('password_lenght', ''.$this->lang->line('a1097').'', 'required');
            
            $SystemLang['Fpassword_lenght'] = $this->input->post('password_lenght');
            $SystemLang['Fupercase'] = $this->input->post('upercase');
            $SystemLang['Flowercase'] = $this->input->post('lowercase');
            $SystemLang['Fdigits'] = $this->input->post('digits');
            $SystemLang['Funderline'] = $this->input->post('underline');
            $SystemLang['Fspace'] = $this->input->post('space');
            $SystemLang['Fminus'] = $this->input->post('minus');
            $SystemLang['Fspecial'] = $this->input->post('special');
            $SystemLang['Fbrackets'] = $this->input->post('brackets');

			if($this->form_validation->run() != FALSE)
			{
                $ReadyString = null;
                $ReadyStringMore = null;
                
                if($this->input->post('upercase') == 'yes')
                {
    				$CharsUpercase = "ABCDEFGHIJKLMNOPQRSTWUXYZ";
                    $CharsUpercase = str_split($CharsUpercase);
                    $CharsUpercaseRand = array_rand($CharsUpercase, 1);
                    $ReadyString .= $CharsUpercase[$CharsUpercaseRand];
                    $ReadyStringMore .= "ABCDEFGHIJKLMNOPQRSTWUXYZ";
                }
                
                if($this->input->post('lowercase') == 'yes')
                {
                    $CharsLowercase = "abcdefghijklmnoprqstwxyz";
                    $ReadyStringMore .= $CharsLowercase;
                    $CharsLowercase = str_split($CharsLowercase);
                    $CharsLowercaseRand = array_rand($CharsLowercase, 1);
                    $ReadyString .= $CharsLowercase[$CharsLowercaseRand];
                }
                
                if($this->input->post('digits') == 'yes')
                {
                    $CharsDigits = "1234567890";
                    $ReadyStringMore .= $CharsDigits;
                    $CharsDigits = str_split($CharsDigits);
                    $CharsDigitsRand = array_rand($CharsDigits, 1);
                    $ReadyString .= $CharsDigits[$CharsDigitsRand];
                }
                
                if($this->input->post('minus') == 'yes')
                {
                    $CharsMinus = "-";
                    $ReadyString .= $CharsMinus;
                    $ReadyStringMore .= $CharsMinus;
                }
                
                if($this->input->post('underline') == 'yes')
                {
                    $CharsUnderline = "_";
                    $ReadyString .= $CharsUnderline;
                    $ReadyStringMore .= $CharsUnderline;
                }
                
                if($this->input->post('space') == 'yes')
                {
                    $CharsSpace = " ";
                    $ReadyString .= $CharsSpace;
                    $ReadyStringMore .= $CharsSpace;
                }
                
                if($this->input->post('special') == 'yes')
                {
                    $CharsSpecial = "!#$%&'*+,.:;=?@";
                    $ReadyStringMore .= $CharsSpecial;
                    $CharsSpecial = str_split($CharsSpecial);
                    $CharsSpecialRand = array_rand($CharsSpecial, 1);
                    $ReadyString .= $CharsSpecial[$CharsSpecialRand];
                }
                
                if($this->input->post('brackets') == 'yes')
                {
                    $CharsBrackets = "[]{}()";
                    $ReadyStringMore .= $CharsBrackets;
                    $CharsBrackets = str_split($CharsBrackets);
                    $CharsBracketsRand = array_rand($CharsBrackets, 1);
                    $ReadyString .= $CharsBrackets[$CharsBracketsRand];
                }
                
                $HowManyChars = $this->input->post('password_lenght');
                
                $ReadyPassword = null;
                
                $HowManyChars = (int)$HowManyChars;
                
                $ReadyString = str_split($ReadyString);
                
                if($HowManyChars == count($ReadyString))
                {
                    // OK
                    shuffle($ReadyString);                
                    $ReadyPassword = implode('',$ReadyString);
                    
                    //echo 'pass';
                }
                elseif($HowManyChars < count($ReadyString))
                {
                    // OK
                    shuffle($ReadyString);
                    
                    $ReadyString = array_slice($ReadyString,0,$HowManyChars);
                    
                    $ReadyPassword = implode('',$ReadyString);
                    
                    //echo 'pass-small';
                }
                elseif($HowManyChars > count($ReadyString))
                {
                    // OK
                    /*echo '<pre>';
                    print_r($ReadyString);
                    echo '</pre>';*/
                    
                    $NeedToVerify = $HowManyChars - count($ReadyString);
                    
                    //echo '-----'.$NeedToVerify.'--------';
                    
                    /*echo '<pre>1:';
                    print_r($ReadyStringMore);
                    echo '</pre>';*/
                    
                    $ReadyStringMore = str_split($ReadyStringMore);
                    
                    /*echo '<pre>';
                    print_r($ReadyStringMore);
                    echo '</pre>';*/
                    
                    $ReadyString2 = null;
                    
                    $ReadyStringMoreRand = null;
                    
                    for($i=0;$i<$NeedToVerify;$i++)
                    {
                        $ReadyStringMoreRand = array_rand($ReadyStringMore, 1);
                        $ReadyString2 .= $ReadyStringMore[$ReadyStringMoreRand];
                    }
                    
                    //echo '--------ReadyString2: '.$ReadyString2.'-----------';
                    
                    $ReadyPasswordTemp = implode('',$ReadyString);
                    $ReadyPasswordTemp = $ReadyPasswordTemp.$ReadyString2;
                    
                    $ReadyString = str_split($ReadyPasswordTemp);
                    
                    shuffle($ReadyString);                
                    $ReadyPassword = implode('',$ReadyString);
                    
                    //echo 'pass-more';
                }
                
                //echo '---------'.$ReadyPassword.'----------';
            }
        }
        
        $SystemLang['ReadyPassword'] = $ReadyPassword;
        
        $this->load->view('generator', $SystemLang);
            
        $this->load->view('foot');
    }
    
    public function checkispasswordgood($str)
    {
        $GetPassword = false;
        
        if($str == $_SESSION['user_password'])
        {
            $GetPassword = true;   
        }
        
        if($GetPassword)
        {
            return TRUE;
        }
        else
        {
            $this->form_validation->set_message('checkispasswordgood', ''.$this->lang->line('a1145').'');
			return FALSE;
        }
    }
    
    public function main()
    {
        if($_SESSION['user_id'] == ""){redirect();exit();}
        
        $SystemLang['Title'] = ''.$this->lang->line('a1092').'';
        $SystemLang['Content'] = ''.$this->lang->line('a1093').'';
        
        $SystemLang['PassField'] = true;
        
        if($this->input->post('formlogin') == 'yes')
		{
            $this->form_validation->set_rules('pass_old', ''.$this->lang->line('a1146').'', 'required|callback_checkispasswordgood');
			$this->form_validation->set_rules('pass_password', ''.$this->lang->line('a1147').'', 'required');
            $this->form_validation->set_rules('pass_password_rec', ''.$this->lang->line('a1148').'', 'required|matches[pass_password]');

            if($this->form_validation->run() != FALSE)
    		{
                //echo 'OK';
                $this->System_model->SetUserNewPasswordInTheSystem();
                redirect('logout');
            }
        }
        
        $this->load->view('head',$SystemLang);
        
        $this->load->view('main', $SystemLang);
            
        $this->load->view('foot');
    }
    
    public function usedcomponents()
    {
        //if($_SESSION['user_id'] == ""){redirect();exit();}
        
        $SystemLang['Title'] = ''.$this->lang->line('a1069').'';
        $SystemLang['Content'] = ''.$this->lang->line('a1070').'';
        
        $this->load->view('head',$SystemLang);
        
        $this->load->view('components', $SystemLang);
            
        $this->load->view('foot');
    }
    
    public function about()
    {
        //if($_SESSION['user_id'] == ""){redirect();exit();}
        
        $SystemLang['Title'] = ''.$this->lang->line('a1090').'';
        $SystemLang['Content'] = ''.$this->lang->line('a1091').'';
        
        $this->load->view('head',$SystemLang);
        
        $this->load->view('about', $SystemLang);
            
        $this->load->view('foot');
    }
    
    public function logout()
    {
        /*if($_SESSION['user_id'] == "")
        {
            redirect('');
            exit();
        }*/
        
        $_SESSION['user_id'] = '';
        $_SESSION['user_password'] = '';
        $_SESSION['user_iv'] = '';

        redirect();
    }
}

?>