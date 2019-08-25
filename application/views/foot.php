<br /><br /><br /><br />
<?php
//echo '<pre>';
//print_r($_SESSION);
//echo '</pre>';
?>
</div>

<div style="position: fixed;
    bottom: 0;
    width: 100%; background-color: #27313D; border-top: solid 3px #2BABD2;">
    <div class="container" style="margin-top: 5px;">
        <div style="padding: 5px; color: #A6A299;">
        Copyright &copy; 2016-2018 - by 
        <a href="http://phpbluedragon.eu" target="_blank" style="color: #ffffff;">phpBlueDragon PassWeb</a> | 
        <a href="<?php echo base_url('components'); ?>" style="color: #ffffff;"><?php echo $this->lang->line('a1069'); ?></a> | 
        <a href="<?php echo base_url('about'); ?>" style="color: #ffffff;"><?php echo $this->lang->line('a1105'); ?></a>
        </a>
        </div>
    </div>
</div>
<script src="<?php echo base_url(); ?>library/tether/js/tether.min.js"></script>
<script src="<?php echo base_url(); ?>library/password_indicator_bs4/js/jaktutorial.js"></script>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery("#pass_password").keyup(function() {
	  passwordStrength(jQuery(this).val());
	});
});
// Add/Edit Password
<?php
if($PassField)
{
    echo 'passwordStrength($("#pass_password").val());';
}
?>
</script>
</body>
</html>