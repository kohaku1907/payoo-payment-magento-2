/**********************************************************************
 *
 * @copyright Copyright © Kohaku1907. All rights reserved.
 * @author    nguyenmtri11@gmail.com
 */
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';

        rendererList.push(
            {
                type: 'payoo',
                component: 'Kohaku1907_PayooPayment/js/view/payment/method-renderer/payoo-payment'
            }
        );

        /**
         * Add view logic here if needed
         */

        return Component.extend({});
    }
);
