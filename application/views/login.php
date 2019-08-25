<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author:    Lukasz Sosna
 * @e-mail:    lukasz.bluedragon@gmail.com
 * @www:       http://phpbluedragon.pl
 * @copyright: 10-7-2015 18:45
 *
 */
 
?>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('a1073'); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo $this->lang->line('a1074'); ?></li>
  </ol>
</nav>
<?php

echo '<h1 class="text-center"><span class="fa fa-id-card-o"></span> '.$Title.'</h1>';

if($HowManyRecords != 0)
{
    echo '<p class="text-center">'.$Content.'</p>';
}

//echo $Content.'<br />';

$ResultDB = $this->System_model->GetPasswordsList();
        
$OptionsList = null;

foreach($ResultDB->result() as $row)
{
    $OptionsList[$row->passwordlist_id] = $row->passwordlist_name;
}

if($HowManyRecords != 0)
{
    echo '<div class="row">
      <div class="col-md-2"></div>
      <div class="col-md-8">';
    
    if($bad_data == TRUE)
    {
    	echo '<br /><div class="alert alert-danger" role="alert">'.$this->lang->line('a0946').'</div>';	
    }
    
    //form_input(array('name' => 'user_email', 'id' => 'user_email', 'class' => 'form-control'))
    
    echo form_open('login');
    echo '<br /><strong>'.$this->lang->line('a1078').'</strong> <br /> '.form_dropdown('passwordlist_name', $OptionsList, $Fpassword_list_name, 'class="form-control"').'<br />';
    echo form_error('passwordlist_name','<div class="alert alert-danger">', '</div>');
    echo '<strong>'.$this->lang->line('a0948').'</strong> <br /> '.form_password(array('name' => 'passwordlist_password', 'id' => 'passwordlist_password', 'value' => '', 'class' => 'form-control')).' <br />';
    echo form_error('passwordlist_password','<div class="alert alert-danger">', '</div>');
    echo form_hidden('formlogin','yes');
    echo form_submit(array('name' => 'buttonstart', 'value' => ''.$this->lang->line('a0949').'', 'class' => 'btn btn-info btn-block'));
    echo form_close();
    
    echo '</div>
      <div class="col-md-2"></div>
    </div><br /><br />';
}
else
{
    echo '<p class="text-center">'.$this->lang->line('a1075').'</p>';
}

echo '<h2 class="text-center"><span class="fa fa-file-o"></span> '.$this->lang->line('a1076').'</h2>';
    
    echo '<br /><div class="row">
      <div class="col-md-2"></div>
      <div class="col-md-8">';
    
    if($new_repo_created)
    {
        echo '<p class="alert alert-success" role="alert">'.$this->lang->line('a1083').'</p>';
    }
    
    echo form_open('login');
    
    echo '<strong>'.$this->lang->line('a1082').'</strong> <br /> '.form_input(array('name' => 'passwordlist_name', 'id' => 'passwordlist_name', 'value' => '', 'class' => 'form-control')).' <br />';
    echo form_error('passwordlist_name','<div class="alert alert-danger">', '</div>');
    
    echo '<strong>'.$this->lang->line('a0948').'</strong> <br /> '.form_password(array('name' => 'pass_password', 'id' => 'pass_password', 'value' => '', 'class' => 'form-control')).'';
    echo '<div class="progress" style="margin-top: 5px;">
      <div class="progress-bar progress-bar-striped progress-bar-animated jak_pstrength" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
    </div>';
    echo form_error('pass_password','<div class="alert alert-danger">', '</div>');
    echo '<br />';
    
    echo '<strong>'.$this->lang->line('a1081').'</strong> <br /> '.form_password(array('name' => 'pass_password2', 'id' => 'pass_password2', 'value' => '', 'class' => 'form-control')).' <br />';
    echo form_error('pass_password2','<div class="alert alert-danger">', '</div>');
    
    echo form_hidden('formnewrepo','yes');
    echo form_submit(array('name' => 'buttonstart2', 'value' => ''.$this->lang->line('a1080').'', 'class' => 'btn btn-info btn-block'));
    echo form_close();
    
    echo '</div>
      <div class="col-md-2"></div>
    </div><br /><br />';
    
?>