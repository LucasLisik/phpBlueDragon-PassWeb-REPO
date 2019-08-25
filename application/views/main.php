<?php  

echo '<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="'.base_url().'">'.$this->lang->line('a1073').'</a></li>
    <li class="breadcrumb-item active" aria-current="page">'.$this->lang->line('a1092').'</li>
  </ol>
</nav>';

echo '<div class="bodytext2">';

echo '<h1><span class="fa fa-cog"></span> '.$Title.'</h1>';

echo $Content;

echo '<div style="max-width: 400px; padding: 10px;" class="container">';

if($password_is_bad)
{
    echo '<div class="alert alert-warning">'.$this->lang->line('a1183').'</div>';
}

echo form_open('main');

echo '<strong>'.$this->lang->line('a1184').'</strong> <br /> '.form_password(array('name' => 'pass_old', 'id' => 'pass_old', 'class' => 'form-control')).'<br />';
echo form_error('pass_old','<div class="alert alert-danger">','</div>');

echo '<strong>'.$this->lang->line('a1185').'</strong> <br /> '.form_password(array('name' => 'pass_password', 'id' => 'pass_password', 'class' => 'form-control'));
echo '<div class="progress" style="margin-top: 5px;"><div class="progress-bar progress-bar-striped progress-bar-animated jak_pstrength" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
</div>';
echo form_error('pass_password','<div class="alert alert-danger">','</div>');
echo '<br />';

echo '<strong>'.$this->lang->line('a1186').'</strong> <br /> '.form_password(array('name' => 'pass_password_rec', 'id' => 'pass_password_rec', 'class' => 'form-control')).'<br />';
echo form_error('pass_password_rec','<div class="alert alert-danger">','</div>');
  
echo form_hidden('formlogin','yes');
echo form_submit(array('name' => 'buttonstart', 'value' => ''.$this->lang->line('a1187').'', 'class' => 'btn btn-info btn-block'));
echo form_close();

echo '</div>';

echo '</div>';

?>