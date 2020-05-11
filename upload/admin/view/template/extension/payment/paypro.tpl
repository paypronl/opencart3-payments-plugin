<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-paypro" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1><?php echo $heading_title ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-paypro" class="form-horizontal">
            <?php foreach ($shops as $shop) { ?>
            <?php if ($error_warning) { ?>
            <div class="alert alert-danger alert-dismissable">
                <i class="fa fa-exclamation-circle"></i>
                <?php echo $shop['name']; ?>: <?php echo $error_warning; ?>
                <button type="button" class="close" data-dismiss="alert">&times;
                </button>
            </div>
            <?php } ?>
            <?php } ?>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $paypro_config_title; ?></h3>
                </div>
                <div class="panel-body">
                    <ul class="nav nav-tabs">
                        <?php foreach ($shops as $shop) { ?>
                        <li class="<?php echo $shop['id'] === 0 ? 'active' : ''; ?>"><a data-toggle="tab" href="#store<?php echo $shop['id']; ?>"><?php echo $shop['name']; ?></a></li>
                        <?php } ?>
                    </ul>

                    <div class="tab-content">
                        <?php foreach ($shops as $shop) { ?>
                        <div id="store<?php echo $shop['id']; ?>" class="tab-pane fade in <?php echo $shop['id'] === 0 ? 'active' : ''; ?>">
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#paypro-config-<?php echo $shop['id']; ?>"><?php echo $main_config_title; ?></a></li>
                                <li><a data-toggle="tab" href="#payment-statuses-<?php echo $shop['id']; ?>"><?php echo $status_config_title; ?></a></li>
                                <li><a data-toggle="tab" href="#about-<?php echo $shop['id']; ?>"><?php echo $about_title; ?></a></li>
                            </ul>

                            <div class="tab-content">
                                <div id="paypro-config-<?php echo $shop['id']; ?>" class="tab-pane fade in active">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="test_mode"><?php echo $entry_test_mode; ?></label>
                                        <div class="col-sm-10">
                                            <select name="stores[<?php echo $shop['id']; ?>][test_mode]" id="test_mode" class="form-control">
                                                <?php if ($stores[$shop['id']]['test_mode']) { ?>
                                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                                <option value="0"><?php echo $text_disabled; ?></option>
                                                <?php } else { ?>
                                                <option value="1"><?php echo $text_enabled; ?></option>
                                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group required">
                                        <label class="col-sm-2 control-label" for="api_key"><?php echo $entry_api_key; ?></label>
                                        <div class="col-sm-10">
                                            <input type="text" name="stores[<?php echo $shop['id']; ?>][api_key]" value="<?php echo $stores[$shop['id']]['api_key']; ?>" id="api_key" class="form-control"/>
                                            <?php if ($stores[$shop['id']]['error_api_key']) { ?>
                                            <div class="text-danger"><?php echo $stores[$shop['id']]['error_api_key']; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="product_id"><?php echo $entry_product_id; ?></label>
                                        <div class="col-sm-10">
                                            <input type="text" name="stores[<?php echo $shop['id']; ?>][product_id]" value="<?php echo $stores[$shop['id']]['product_id']; ?>" id="product_id" class="form-control"/>
                                            <div class="text-warning"><?php echo $info_product_id; ?></div>
                                        </div>
                                    </div>
                                    <?php foreach ($payment_methods as $index => $method) { ?>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label"><?php echo $method['label'] ?></label>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label" for="<?php echo $method['id']; ?>_status"><?php echo $entry_status; ?></label>
                                            <div class="col-sm-9">
                                                <select name="stores[<?php echo $shop['id']; ?>][<?php echo $method['id']; ?>_status]" id="<?php echo $method['id']; ?>_status" class="form-control">
                                                    <?php if ($stores[$shop['id']][$method['id'] . '_status']) { ?>
                                                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                                    <option value="0"><?php echo $text_disabled; ?></option>
                                                    <?php } else { ?>
                                                    <option value="1"><?php echo $text_enabled; ?></option>
                                                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label" for="<?php echo $method['id']; ?>_geo_zone"><?php echo $entry_geo_zone; ?></label>
                                        <div class="col-sm-9">
                                            <select name="stores[<?php echo $shop['id']; ?>][<?php echo $method['id']; ?>_geo_zone]" id="<?php echo $method['id']; ?>_geo_zone" class="form-control">
                                                <option value="0"><?php echo $text_all_zones; ?></option>
                                                <?php foreach ($geo_zones as $geo_zone) { ?>
                                                <?php if ($geo_zone['geo_zone_id'] === $stores[$shop['id']][$method['id'] . '_geo_zone']) { ?>
                                                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                                                <?php } else { ?>
                                                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                                                <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label" for="<?php echo $method['id']; ?>_sort_order"><?php echo $entry_sort_order; ?></label>
                                            <div class="col-sm-9">
                                                <input type="text" name="stores[<?php echo $shop['id']; ?>][<?php echo $method['id']; ?>_sort_order]" value="<?php echo $stores[$shop['id']][$method['id'] . '_sort_order']; ?>" placeholder="<?php echo $index + 1; ?>" id="<?php echo $method['id']; ?>_sort_order" class="form-control" />
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>

                                <div id="payment-statuses-<?php echo $shop['id']; ?>" class="tab-pane fade in">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="pending_status_id"><?php echo $pending_status; ?></label>
                                        <div class="col-sm-10">
                                            <select name="stores[<?php echo $shop['id']; ?>][pending_status_id]" id="pending_status_id" class="form-control">
                                                <?php foreach ($order_statuses as $order_status) { ?>
                                                <?php if ($order_status['order_status_id'] == $stores[$shop['id']]['pending_status_id']) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                                <?php } else { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                                <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="failed_status_id"><?php echo $failed_status; ?></label>
                                        <div class="col-sm-10">
                                            <select name="stores[<?php echo $shop['id']; ?>][failed_status_id]" id="failed_status_id" class="form-control">
                                                <?php foreach ($order_statuses as $order_status) { ?>
                                                <?php if ($order_status['order_status_id'] == $stores[$shop['id']]['failed_status_id']) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                                <?php } else { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                                <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="canceled_status_id"><?php echo $canceled_status; ?></label>
                                        <div class="col-sm-10">
                                            <select name="stores[<?php echo $shop['id']; ?>][canceled_status_id]" id="canceled_status_id" class="form-control">
                                                <?php foreach ($order_statuses as $order_status) { ?>
                                                <?php if ($order_status['order_status_id'] == $stores[$shop['id']]['canceled_status_id']) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                                <?php } else { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                                <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="expired_status_id"><?php echo $expired_status; ?></label>
                                        <div class="col-sm-10">
                                            <select name="stores[<?php echo $shop['id']; ?>][expired_status_id]" id="expired_status_id" class="form-control">
                                                <?php foreach ($order_statuses as $order_status) { ?>
                                                <?php if ($order_status['order_status_id'] == $stores[$shop['id']]['expired_status_id']) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                                <?php } else { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                                <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="completed_status_id"><?php echo $completed_status; ?></label>
                                        <div class="col-sm-10">
                                            <select name="stores[<?php echo $shop['id']; ?>][completed_status_id]" id="completed_status_id" class="form-control">
                                                <?php foreach ($order_statuses as $order_status) { ?>
                                                <?php if ($order_status['order_status_id'] == $stores[$shop['id']]['completed_status_id']) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                                <?php } else { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                                <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div id="about-<?php echo $shop['id']; ?>" class="tab-pane fade in">
                                    <fieldset>
                                        <legend>PayPro - <a href="https://paypro.nl" target="_blank">PayPro.nl</a></legend>
                                        <div class="row">
                                            <label class="col-sm-2">Support</label>
                                            <div class="col-sm-10">
                                                Contact us <a href="https://guide.paypro.nl/nl/" target="_blank">here</a>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?php echo $footer; ?>
