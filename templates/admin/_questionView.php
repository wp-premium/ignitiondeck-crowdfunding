<?php if ($question_view) { ?>
<table border="0" width="100%">
	<tr>
		<td colspan="2"><b><?php echo $tr_Question_Details; ?></b></td>
	</tr>
	<tr>
		<td class="label"><?php echo $tr_Name; ?></td>
		<td class="field"><?php echo $question_data->full_name ?></td>
	</tr>
	<tr>
		<td class="label"><?php echo $tr_Email; ?></td>
		<td class="field"><?php echo $question_data->email ?></td>
	</tr>
	<tr>
		<td class="label"><?php echo $tr_Subject; ?></td>
		<td class="field"><?php echo $question_data->subject ?>
			</td>
	</tr>
	<tr>
		<td class="label"><?php echo $tr_Message; ?></td>
		<td class="field"><?php echo $question_data->comments ?>
			</td>
	</tr>
</table>
<?php } else { ?>
<div class="wrap">
    <table class="wp-list-table">
        <thead>
            <tr>
                <th><b><!--<?php echo $tr_Sr_no; ?>--></b></th>
                <th><b><?php echo $tr_Question_Subject; ?></b></th>
				<th><b><?php echo $tr_Created_Date; ?></b></th>
                <th><b><?php echo $tr_Action; ?></b></th>
            </tr>
        </thead>
        <?php $count = 1;for($i=0,$n=count($questions);$i<$n;$i++){$item = &$questions[$i];?>
        <tr>
            <td valign="top"><?php echo $count;?></td>
            <td valign="top"><?php echo $item->subject;?></td>
			<td valign="top"><?php echo date('m/d/Y', strtotime($item->created_date));?></td>
            <td><a href="?page=asked_questions&quesid=<?php echo $item->id;?>"><?php echo $tr_View; ?></a> | <a href="?page=asked_questions&action=delete&quesid=<?php echo $item->id;?>" onclick="return confirm('Are you sure you want to delete this question?');"><?php echo $tr_Delete; ?></a></td>
        </tr>
        <?php
        $count++;
        }?>
    </table>
    <?php echo $pagination;?>
</div>
<?php } ?>