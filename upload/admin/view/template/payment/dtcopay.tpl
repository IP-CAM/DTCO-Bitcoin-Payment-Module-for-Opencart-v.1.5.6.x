<?php echo $header; ?>

<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<div class="box">
  <div class="left"></div>
  <div class="right"></div>
    <div class="heading">
      <h1><img src="view/image/payment.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
    </div>
  <div class="content">
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
      <table class="form">
        <tr>
          <td><span class="required">*</span> <?php echo $entry_api_key; ?></td>
          <td><input type="text" name="dtcopay_api_key" value="<?php echo $dtcopay_api_key; ?>" style="width:300px;" />
            <?php if ($error_api_key) { ?>
            <span class="error"><?php echo $error_api_key; ?></span>
            <?php } ?></td>
        </tr>
          <tr>
            <td><?php echo $entry_confirmed_status; ?></td>
            <td><select name="dtcopay_confirmed_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $dtcopay_confirmed_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_invalid_status; ?></td>
            <td><select name="dtcopay_invalid_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $dtcopay_invalid_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_transaction_speed; ?></td>
            <td><select name="dtcopay_transaction_speed">
                <?php if ($dtcopay_transaction_speed == 'high') { ?>
                <option value="high" selected="selected"><?php echo $text_high; ?></option>
                <?php } else { ?>
                <option value="high"><?php echo $text_high; ?></option>
                <?php } ?>
                <?php if ($dtcopay_transaction_speed == 'medium') { ?>
                <option value="medium" selected="selected"><?php echo $text_medium; ?></option>
                <?php } else { ?>
                <option value="medium"><?php echo $text_medium; ?></option>
                <?php } ?>
                <?php if ($dtcopay_transaction_speed == 'low') { ?>
                <option value="low" selected="selected"><?php echo $text_low; ?></option>
                <?php } else { ?>
                <option value="low"><?php echo $text_low; ?></option>
                <?php } ?>
              </select></td>
          </tr>
        <tr>
          <td><?php echo $entry_status; ?></td>
          <td><select name="dtcopay_status"> 
              <?php if ($dtcopay_status) { ?>
              <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
              <option value="0"><?php echo $text_disabled; ?></option>
              <?php } else { ?>
              <option value="1"><?php echo $text_enabled; ?></option>
              <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
              <?php } ?>
            </select></td>
        </tr>
        <tr>
          <td><?php echo $entry_sort_order; ?></td>
          <td><input type="text" name="dtcopay_sort_order" value="<?php echo $dtcopay_sort_order; ?>" size="1" /></td>
        </tr>
      </table>
    </form>
  </div>
</div>
</div>
<?php echo $footer; ?>
