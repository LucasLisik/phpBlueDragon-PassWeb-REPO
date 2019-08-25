<?php

echo '<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="'.base_url().'">'.$this->lang->line('a1073').'</a></li>
    <li class="breadcrumb-item active" aria-current="page">'.$this->lang->line('a1094').'</li>
  </ol>
</nav>';

echo '<div class="bodytext2">';

echo '<h1><span class="fa fa-hourglass-half"></span> '.$Title.'</h1>';

echo $Content;

for($i=1;$i<121;$i++)
{
    $OptionsList[$i] = $i;
}

if($Fpassword_lenght == "")
{
    $Fpassword_lenght = 26;
}

echo '<br /><br />';

$FupercaseSelect = false;
if($Fupercase == "yes")
{
    $FupercaseSelect = true;
}

$FlowercaseSelect = false;
if($Flowercase == "yes")
{
    $FlowercaseSelect = true;
}

$FdigitsSelect = false;
if($Fdigits == "yes")
{
    $FdigitsSelect = true;
}

$FunderlineSelect = false;
if($Funderline == "yes")
{
    $FunderlineSelect = true;
}

$FspaceSelect = false;
if($Fspace == "yes")
{
    $FspaceSelect = true;
}

$FminusSelect = false;
if($Fminus == "yes")
{
    $FminusSelect = true;
}

$FspecialSelect = false;
if($Fspecial == "yes")
{
    $FspecialSelect = true;
}

$FbracketsSelect = false;
if($Fbrackets == "yes")
{
    $FbracketsSelect = true;
}

?>
<script>
function copyToClipboard(element) 
{
  var $temp = $("<input>");
  $("body").append($temp);
  $temp.val($(element).text()).select();
  document.execCommand("copy");
  $temp.remove();
  
  $("div#infocopied").show("fast").delay(3000).fadeOut();
}

</script>
<?php

if($ReadyPassword != "")
{
    echo '<div class="alert alert-success text-center">
      <span id="generatedpassword">'.$ReadyPassword.'</span> <br /><br />
      <div id="infocopied" style="display: none;">'.$this->lang->line('a1173').'<br /><br /></div>
      <button onclick="copyToClipboard(\'#generatedpassword\');" class="btn btn-info">'.$this->lang->line('a1174').'</button>
    </div>';
}

echo form_open('generator');
echo '<strong>'.$this->lang->line('a1096').'</strong><br />';
echo form_error('upercase','<br /><div class="alert alert-danger">', '</div>');
echo form_checkbox('upercase', 'yes', $FupercaseSelect).''.$this->lang->line('a1175').'<br />';
echo form_checkbox('lowercase', 'yes', $FlowercaseSelect).''.$this->lang->line('a1176').'<br />';
echo form_checkbox('digits', 'yes', $FdigitsSelect).''.$this->lang->line('a1177').'<br />';
echo form_checkbox('underline', 'yes', $FunderlineSelect).''.$this->lang->line('a1178').'<br />';
echo form_checkbox('space', 'yes', $FspaceSelect).''.$this->lang->line('a1179').'<br />';
echo form_checkbox('minus', 'yes', $FminusSelect).''.$this->lang->line('a1180').'<br />';
echo form_checkbox('special', 'yes', $FspecialSelect).''.$this->lang->line('a1181').'<br />';
echo form_checkbox('brackets', 'yes', $FbracketsSelect).''.$this->lang->line('a1182').'<br />';

echo '<br /><strong>'.$this->lang->line('a1097').'</strong> <br /> '.form_dropdown('password_lenght', $OptionsList, $Fpassword_lenght, 'class="form-control"').'<br />';
echo form_error('password_lenght','<div class="alert alert-danger">', '</div>');

echo form_hidden('formgenerator','yes');
echo form_submit(array('name' => 'buttonstart', 'value' => ''.$this->lang->line('a1098').'', 'class' => 'btn btn-info btn-block'));
echo form_close();

?>
</div>