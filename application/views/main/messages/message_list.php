<?php $this->load->view('js_init/message/js_object');
if($messages->num_rows()==0) 
{
	$no_message_container['start'] = "<p class=\"no_content\"><span class=\"ui-icon ui-icon-alert\" style=\"float:left;\"></span><i>";
	$no_message_container['end'] = "</i></p>";
	
	if($this->uri->segment(2)=='my_folder') 
	{
		if($this->uri->segment(4)=='5') echo $no_message_container['start'].lang('tni_msglist_trash_empty').$no_message_container['end'];
		else echo $no_message_container['start'].lang('kalkun_no_message_in_folder').$no_message_container['end'];
	}
	else echo $no_message_container['start'].lang('kalkun_no_message')." ".lang('kalkun_'.$this->uri->segment(3)).$no_message_container['end'];
}
else 
{
	// loop - begin
	foreach($messages->result() as $tmp):
		
	// initialization
	$type = $this->uri->segment(3);
	if($type == 'inbox') 
	{
		$qry = $this->Phonebook_model->getPhonebook(array('option'=>'bynumber','number'=>$tmp->SenderNumber));
		if($qry->num_rows()!=0) $senderName = $qry->row('Name');
		else $senderName = $tmp->SenderNumber;
		$number = $tmp->SenderNumber;
		
		$message_date = $tmp->ReceivingDateTime;
		$arrow = 'arrow_left';
	}
	else 
	{
		$qry = $this->Phonebook_model->getPhonebook(array('option'=>'bynumber','number'=>$tmp->DestinationNumber));
		if($qry->num_rows()!=0) $senderName = $qry->row('Name');
		else $senderName = $tmp->DestinationNumber;
		$number = $tmp->DestinationNumber;
		
		$message_date = $tmp->SendingDateTime;
		if($type == 'outbox') $arrow = 'circle';
		else $arrow = 'arrow_right';
	}
		
	// count string for message preview
	$char_per_line = 90-strlen(nice_date($message_date))-strlen($senderName);
	?>
	
	<div class="messagelist <?php  if($type == 'inbox' && $tmp->readed=='false') echo "unreaded";?>">
	<div class="message_container">
		<div class="message_header" style="color: #444; height: 20px; overflow: hidden">
		<input type="checkbox" id="<?php echo $number;?>" class="select_conversation nicecheckbox" value="<?php echo $number;?>" />
		<span class="message_toggle" style="cursor: pointer;" onclick="document.location.href='<?php echo site_url();?>/messages/conversation/<?php echo $this->uri->segment(2);?>/<?php echo $this->uri->segment(3);?>/<?php echo AsciiToHex(base64_encode($number));?>/<?php echo $this->uri->segment(4);?>'">
		<span <?php  if($type == 'inbox' && $tmp->readed=='false') echo "style=\"font-weight: bold\"";?>><?php echo nice_date($message_date);?>&nbsp;&nbsp;<img src="<?php echo $this->config->item('img_path').$arrow;?>.gif" />
		&nbsp;&nbsp;<?php echo $senderName;?>
		<?php 
			if($this->uri->segment(2)=='folder'):
			echo "(".$this->Message_model->get_messages(array('type' => $this->uri->segment(3), 'number' => $number))->num_rows().")";
			else:
			echo "(".$this->Message_model->get_messages(array('type' => $this->uri->segment(3), 'number' => $number, 'id_folder' => $this->uri->segment(4)))->num_rows().")";
			endif;
		?>
		</span>
		<span class="message_preview" <?php  if($type == 'inbox' && $tmp->readed=='false') echo "style=\"font-weight: bold\"";?>>-&nbsp;<?php echo message_preview($tmp->TextDecoded, $char_per_line);?></span>
		</span>
		</div>		
	</div></div>
		
		<?php 
		endforeach;
	}
?>
