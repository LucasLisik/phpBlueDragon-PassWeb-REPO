<?php

if($action == 'add')
{
    $TextEditAdd = ''.$this->lang->line('a1160').'';
}
else
{
    $TextEditAdd = ''.$this->lang->line('a1161').'';
}

if(count($QueryResultTable) == 1)
{
    //print_r($QueryResultTable);
    echo '<nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="'.base_url().'">'.$this->lang->line('a1073').'</a></li>
            <li class="breadcrumb-item"><a href="'.base_url('index/'.$QueryResultTable[0]['id']).'">'.$this->System_model->decrypt($QueryResultTable[0]['name'],$_SESSION['user_iv'],$_SESSION['user_password']).'</a></li>
            <li class="breadcrumb-item active" aria-current="page">'.$TextEditAdd.'</li>
        </ol>
    </nav>';
}
else
{
    echo '<nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="'.base_url().'">'.$this->lang->line('a1073').'</a></li>
            <li class="breadcrumb-item"><a href="'.base_url('index/'.$QueryResultTable[0]['id']).'">'.$this->System_model->decrypt($QueryResultTable[0]['name'],$_SESSION['user_iv'],$_SESSION['user_password']).'</a></li>
            <li class="breadcrumb-item"><a href="'.base_url('index/'.$QueryResultTable[1]['id']).'">'.$this->System_model->decrypt($QueryResultTable[1]['name'],$_SESSION['user_iv'],$_SESSION['user_password']).'</a></li>
            <li class="breadcrumb-item active" aria-current="page">'.$TextEditAdd.'</li>
        </ol>
    </nav>';
}

echo '<div class="bodytext2">';

echo '<h1><span class="fa fa-paperclip"></span> '.$Title.'</h1>';

echo $Content.'<br /><br />';


if($new_password_added)
{
    echo '<div class="alert alert-success">'.$this->lang->line('a1162').'</div>';
}

if($password_edited)
{
    echo '<div class="alert alert-success">'.$this->lang->line('a1163').'</div>';
}

if($action == 'add')
{
    $ButtonLang = ''.$this->lang->line('a1160').'';
    echo form_open('addnewpassword/'.$CategoryId);
}
else
{
    $ButtonLang = ''.$this->lang->line('a1161').'';
    echo form_open('editpassword/'.$CategoryId.'/'.$PasswordId);
}

echo '<strong>'.$this->lang->line('a1164').'</strong> <br /> '.form_input(array('name' => 'pass_title', 'id' => 'pass_title', 'value' => $Vpass_title, 'class' => 'form-control')).'<br />';
echo form_error('pass_title','<div class="alert alert-danger">','</div>');

echo '<strong>'.$this->lang->line('a1165').'</strong> <br /> '.form_input(array('name' => 'pass_user', 'id' => 'pass_user', 'value' => $Vpass_user, 'class' => 'form-control')).'<br />';
echo form_error('pass_user','<div class="alert alert-danger">','</div>');

echo '<strong>'.$this->lang->line('a1166').'</strong> <br /> '.form_password(array('name' => 'pass_password', 'id' => 'pass_password', 'value' => $Vpass_password, 'class' => 'form-control'));
echo '<div class="progress" style="margin-top: 5px;">
  <div class="progress-bar progress-bar-striped progress-bar-animated jak_pstrength" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
</div>';
echo form_error('pass_password','<div class="alert alert-danger">','</div>');
echo '<br />';

echo '<strong>'.$this->lang->line('a1167').'</strong> <br /> '.form_password(array('name' => 'pass_password2', 'id' => 'pass_password2', 'value' => $Vpass_password2, 'class' => 'form-control')).'<br />';
echo form_error('pass_password2','<div class="alert alert-danger">','</div>');

echo '<strong>'.$this->lang->line('a1168').'</strong> <br /> '.form_input(array('name' => 'pass_url', 'id' => 'pass_url', 'value' => $Vpass_url, 'class' => 'form-control')).'<br />';
echo form_error('pass_url','<div class="alert alert-danger">','</div>');

$Vpass_note = str_replace('<br />', '', $Vpass_note);

echo '<strong>'.$this->lang->line('a1169').'</strong> <br /> '.form_textarea(array('name' => 'pass_note', 'id' => 'pass_note', 'value' => $Vpass_note, 'class' => 'form-control')).'<br />';
echo form_error('pass_note','<div class="alert alert-danger">','</div>');

            
//echo form_hidden('project_id',$ProjectId);
echo form_hidden('formlogin','yes');
echo form_submit(array('name' => 'buttonstart', 'value' => $ButtonLang, 'class' => 'btn btn-info btn-block'));
echo form_close();

echo '</div>';

?>