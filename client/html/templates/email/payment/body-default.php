<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

?>
<?php $this->block()->start( 'email/payment' ); ?>
<?php echo $this->get( 'paymentBody' ); ?>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'email/payment' ); ?>