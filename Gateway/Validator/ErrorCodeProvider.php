<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Xpanse\Payment\Gateway\Validator;

/**
 * Processes errors codes from Xpanse response.
 */
class ErrorCodeProvider
{
    /**
     * Retrieves list of error codes from Xpanse response.
     *
     * @param  $response
     * @return array
     */
    public function getErrorCodes($response): array
    {
        $result = [];

        if (!empty($response['errorCode'])) {
            $result[] = $response['errorCode'];
        }

        return $result;
    }
}
