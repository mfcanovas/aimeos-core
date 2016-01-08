<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

$enc = $this->encoder();

?>
<div class="product-item-selection card panel">
	<div id="product-item-selection-head" class="header card-header" role="tab"
		data-toggle="collapse" data-parent="#accordion" href="#product-item-selection-data"
		aria-expanded="true" aria-controls="product-item-selection-data">
		<?php echo $enc->html( $this->translate( 'admin', 'Variants' ) ); ?>
	</div>
	<div id="product-item-selection-data" class="item-selection card-block panel-collapse collapse"
		role="tabpanel" aria-labelledby="product-item-selection-head">

<?php foreach( (array) $this->get( 'selectionData', array() ) as $code => $map ) : ?>
			<div class="product-item-selection-groupitem card">
				<div id="product-item-selection-groupitem-<?php echo $enc->attr( $code ); ?>" class="header card-header">
					<?php echo $enc->html( $this->translate( 'admin', $code ) ); ?>
					<div class="actions">
						<div class="btn btn-secondary fa fa-files-o"></div>
						<div class="btn btn-danger fa fa-trash"></div>
					</div>
				</div>
				<div id="product-item-selection-groupdata-<?php echo $enc->attr( $code ); ?>" class="item-selection card-block">
					<div class="col-lg-6">
						<div class="form-group row">
							<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'ID' ) ); ?></label>
							<div class="col-sm-9">
								<input type="hidden" name="selection[product.id][]" value="<?php echo $enc->attr( $this->value( $map, 'product.id' ) ); ?>" />
								<p class="form-control-static"><?php echo $enc->attr( $this->value( $map, 'product.id' ) ); ?></p>
							</div>
						</div>
						<div class="form-group row mandatory">
							<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Code' ) ); ?></label>
							<div class="col-sm-9">
								<input type="text" class="form-control" name="selection[product.code][]"
									placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Unique code (SKU, EAN)' ) ); ?>"
									value="<?php echo $enc->attr( $code ); ?>">
							</div>
						</div>
						<div class="form-group row mandatory">
							<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Label' ) ); ?></label>
							<div class="col-sm-9">
								<input type="text" class="form-control" name="selection[product.label][]"
									placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Internal label' ) ); ?>"
									value="<?php echo $enc->attr( $this->value( $map, 'product.label' ) ); ?>">
							</div>
						</div>
					</div>
					<div class="col-lg-6">
						<table class="selection-item-attributes table table-default">
							<thead>
								<tr>
									<th><?php echo $enc->html( $this->translate( 'admin', 'Variant attributes' ) ); ?></th>
									<th class="actions"><div class="btn btn-primary fa fa-plus"></div></th>
								</tr>
							</thead>
							<tbody>
<?php foreach( (array) $this->value( $map, 'attr', array() ) as $attrid => $list ) : ?>
								<tr>
									<td>
										<input type="hidden" name="selection[attr][ref][]" value="<?php echo $enc->attr( $code ); ?>" />
										<input type="hidden" name="selection[attr][label][]" value="<?php echo $enc->attr( $this->value( $list, 'label' ) ); ?>" />
										<select class="combobox" name="selection[attr][id][]">
											<option value="<?php echo $enc->attr( $attrid ); ?>" ><?php echo $enc->html( $this->value( $list, 'label' ) ); ?></option>
										</select>
									</td>
									<td class="actions"><div class="btn btn-danger fa fa-trash"></div></td>
								</tr>
<?php endforeach; ?>
								<tr class="prototype">
									<td>
										<input type="hidden" name="selection[attr][ref][]" value="" disabled="disabled />
										<input type="hidden" name="selection[attr][label][]" value="" disabled="disabled />
										<select class="combobox-prototype" name="selection[attr][id][]" disabled="disabled">
										</select>
									</td>
									<td class="actions"><div class="btn btn-danger fa fa-trash"></div></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
<?php endforeach; ?>

	</div>
</div>