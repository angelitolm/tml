<?php

namespace AppBundle;


final class AppEvents
{
    /**
     * The MEMBERSHIP_BUY_VALID
     *
     */
    const MEMBERSHIP_BUY_VALID = 'app.membership.buy_valid';

    /**
     * The MEMBERSHIP_BUY_COMPLETED
     *
     */
    const MEMBERSHIP_BUY_COMPLETED = 'app.membership.buy_completed';

    /**
     * The MEMBERSHIP_BUY_CANCEL
     *
     */
    const MEMBERSHIP_BUY_CANCEL = 'app.membership.buy_cancel';

    /**
     * The TRANSFER_INITIALIZE
     *
     */
    const TRANSFER_INITIALIZE = 'app.transfer.initialize';

    /**
     * The MEMBERSHIP_BUY_CANCEL
     *
     */
    const TRANSFER_COMPLETED = 'app.transfer.completed';

}