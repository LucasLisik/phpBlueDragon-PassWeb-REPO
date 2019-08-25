<?php

if($action == 'add')
{
    $LabelName = ''.$this->lang->line('a1149').'';
}
else
{
    $LabelName = ''.$this->lang->line('a1150').'';
}

echo '<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="'.base_url().'">'.$this->lang->line('a1073').'</a></li>
    <li class="breadcrumb-item active" aria-current="page">'.$LabelName.'</li>
  </ol>
</nav>';

echo '<div class="bodytext2">';

echo '<h1><span class="fa fa-link"></span> '.$Title.'</h1>';

echo $Content.'<br /><br />';


if($new_category_added)
{
    echo '<div class="alert alert-success">'.$this->lang->line('a1151').'</div>';
}

if($category_edited)
{
    echo '<div class="alert alert-success">'.$this->lang->line('a1152').'</div>';
}

if($action == 'add')
{
    $ButtonLang = ''.$this->lang->line('a1149').'';
    echo form_open('addcategory');
}
else
{
    $ButtonLang = ''.$this->lang->line('a1150').'';
    echo form_open('editcategory/'.$CategoryId);
}

echo '<strong>'.$this->lang->line('a1153').'</strong> <br /> '.form_input(array('name' => 'category_name', 'id' => 'category_name', 'value' => $Vcategory_name, 'class' => 'form-control')).'<br />';
echo form_error('category_name','<div class="alert alert-danger">','</div>');

//echo form_hidden('project_id',$ProjectId);
echo form_hidden('formlogin','yes');
echo form_submit(array('name' => 'buttonstart', 'value' => $ButtonLang, 'class' => 'btn btn-info btn-block'));
echo form_close();

echo '</div>';

?>