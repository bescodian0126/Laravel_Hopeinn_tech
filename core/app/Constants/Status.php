<?php

namespace App\Constants;

class Status{

    const ENABLE = 1;
    const DISABLE = 0;

    const YES = 1;
    const NO = 0;

    const VERIFIED = 1;
    const UNVERIFIED = 0;

    const PAYMENT_INITIATE = 0;
    const PAYMENT_SUCCESS = 1;
    const PAYMENT_PENDING = 2;
    const PAYMENT_REJECT = 3;

    const TASK_PURCHASED = 0;
    const TASK_PENDING = 1;
    const TASK_SUCCESS = 2;
    const TASK_REJECT = 3;
    const TASK_GET_BONUS = 4;
    const TASK_FAILED= 5;

    const PACKAGE_PURCHASED = 1;
    const PACKAGE_RELEASED = 2;
    const PACKAGE_NETWORK_BONUS = 3;
    const PACKAGE_INVITE_BONUS = 4;

    CONST TICKET_OPEN = 0;
    CONST TICKET_ANSWER = 1;
    CONST TICKET_REPLY = 2;
    CONST TICKET_CLOSE = 3;

    CONST PRIORITY_LOW = 1;
    CONST PRIORITY_MEDIUM = 2;
    CONST PRIORITY_HIGH = 3;

    const USER_ACTIVE = 1;
    const USER_BAN = 0;

    const KYC_UNVERIFIED = 0;
    const KYC_PENDING = 2;
    const KYC_VERIFIED = 1;

}
