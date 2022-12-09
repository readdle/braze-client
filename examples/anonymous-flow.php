<?php

declare(strict_types=1);

use Readdle\BrazeClient\Braze;
use Readdle\BrazeClient\Exception\BrazeException;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/helpers.php';


$brazeClient = new Braze(getApiKey());
try {
    $aliasValue = createAliasName('com.readdle.PDFExpert5', '0', '2222');
    $email = 'test-braze-174@example.com';

    $appStoreAlias = [
        'alias_name' => $aliasValue,
        'alias_label' => 'AppStore'
    ];
    $stripeAlias = [
        'alias_name'    => 'sub_' . time(),
        'alias_label'   => 'Stripe'
    ];
    $format = 'Y-m-d\TH:i:s.Z\Z';


    // create anonymous user with Alias - AppStore
    $response = $brazeClient->users()->newAlias(['user_aliases' => [ $appStoreAlias ]]);
    waitFor('Wait for API data processing (New Alias - AppStore)', 60);
    if (!$response->isSucceed()) {
        logErrorsOrWarnings($response);
        return;
    }
    //


    // find by Alias for future reference by 'braze_id'
    $response = $brazeClient->users()->byIdentifier(['user_aliases' => [ $appStoreAlias ]]);
    if (!$response->isSucceed()) {
        logErrorsOrWarnings($response);
        return;
    }
    $user = $response->getPayload()['users'][0];
    echo 'Find User by Alias: ' . PHP_EOL;
    print_r($user);
    //


    // add Custom Event using 'braze_id'
    $dt = DateTime::createFromFormat('Y-m-d H:i:s', '2022-12-12 12:11:16');
    $customEvent = [
        'name'              => 'DMG Trial',
        'braze_id'          => $user['braze_id'],
        'time'              => date($format),
        'properties'        => [
            'start_date'    => $dt->format($format),
            'end_date'      => $dt->modify('+1 year')->format($format)
        ],
    ];
    $response = $brazeClient->users()->track(['events' => [ $customEvent ]]);
    waitFor('Wait for API data processing (Track Event)');
    if (!$response->isSucceed()) {
        logErrorsOrWarnings($response);
        return;
    }
    echo 'Add Custom Event status: ' . $response->getStatusCode() . PHP_EOL;
    print_r($response->getPayload());
    //


    // create anonymous user with Stripe Alias -> find it -> add Purchase
    $response = $brazeClient->users()->newAlias(['user_aliases' => [ $stripeAlias ]]);
    waitFor('Wait for API data processing (Stripe Alias)', 60);
    if (!$response->isSucceed()) {
        logErrorsOrWarnings($response);
        return;
    }

    $response = $brazeClient->users()->byIdentifier(['user_aliases' => [ $stripeAlias ]]);
    if (!$response->isSucceed()) {
        logErrorsOrWarnings($response);
        return;
    }
    $userWithEmailAlias = $response->getPayload()['users'][0];
    echo 'Find User by Stripe Alias: ' . PHP_EOL;
    print_r($userWithEmailAlias);

    $dt = DateTime::createFromFormat('Y-m-d H:i:s', '2022-12-13 12:11:16');
    $purchase = [
        'braze_id'          => $userWithEmailAlias['braze_id'],
        'time'              => date($format),
        'product_id'        => 'test_product_id',
        'currency'          => 'USD',
        'price'             => 79.99,
        'properties'        => [
            'type_of_purchase'  => 'DMG Annual',
            'start_date'        => $dt->format($format),
            'end_date'          => $dt->modify('+1 year')->format($format),
            'edu_offer_applied' => false,
            'purchase_offer'    => '',
        ],
    ];
    $response = $brazeClient->users()->track(['purchases' => [ $purchase ]]);
    waitFor('Wait for API data processing (Track Event) - Stripe Alias');
    if (!$response->isSucceed()) {
        logErrorsOrWarnings($response);
        return;
    }
    echo 'Add Purchase status: ' . $response->getStatusCode() . PHP_EOL;
    print_r($response->getPayload());
    //


    // Identify user by assign external_id -> Then add attributes
    $response = $brazeClient->users()->byIdentifier(['user_aliases' => [ $appStoreAlias ]]);
    if (!$response->isSucceed()) {
        logErrorsOrWarnings($response);
        return;
    }
    $users = $response->getPayload()['users'];
    echo 'Find user before /identify request, Users list: ' . PHP_EOL;
    print_r($users);

    //      identify request:
    $externalId = Helper::getUUID();
    $fullStripeAlias = [
        [
            'external_id'   => $externalId,
            'user_alias'    => $stripeAlias
        ]
    ];
    if (!empty($users)) {
        $existingUser = $users[0];
        $oldAliases = [];
        foreach ($existingUser['user_aliases'] ?? [] as $alias) {
            $oldAliases[] = ['external_id' => $externalId, 'user_alias' => $alias];
        }

        // during identify request, aliases will be present in the Final entity
        // only if they were previously created via /users/alias/new API endpoint
        $response = $brazeClient->users()->identify(['aliases_to_identify' => array_merge($oldAliases, $fullStripeAlias)]);
        waitFor('Wait for API (Identify - existing user)');
    } else {
        $response = $brazeClient->users()->identify(['aliases_to_identify' => $fullStripeAlias]);
        waitFor('Wait for API (Identify - new user)');
    }
    if (!$response->isSucceed()) {
        logErrorsOrWarnings($response);
        return;
    }
    echo 'Identify response for user: ' . PHP_EOL;
    print_r($response->getPayload());// only aliases_processed=1/2/etc and message='success' props

    //      add attributes:
    $dt = DateTime::createFromFormat('Y-m-d H:i:s', '2022-12-08 12:11:16');
    $date = $dt->format($format);
    $accountCreationDate    = ['external_id' => $externalId, 'account_creation_date' => $date];
    $onboardingOccupation   = ['external_id' => $externalId, 'onboarding_occupation' => 'Sales'];
    $platform               = ['external_id' => $externalId, 'platform' => ['iphone', 'ipad', 'mac']];
    $profileEmail           = ['external_id' => $externalId, 'email' => $email];
    $response = $brazeClient->users()->track([
        'attributes' => [$accountCreationDate, $onboardingOccupation, $platform, $profileEmail]
    ]);
    waitFor('Wait for API data processing (Track - add new attributes)');
    if (!$response->isSucceed()) {
        logErrorsOrWarnings($response);
        return;
    }
    echo 'Track user attributes status code: ' . $response->getStatusCode() . PHP_EOL;
    print_r($response->getPayload());
    //


    // Final - find by Alias and check: 'braze_id', 'external_id'
    $response = $brazeClient->users()->byIdentifier(['user_aliases' => [ $appStoreAlias ]]);
    if (!$response->isSucceed()) {
        logErrorsOrWarnings($response);
        return;
    }
    $finalUsers = $response->getPayload()['users'];
    echo 'Final User: ' . PHP_EOL;
    print_r($finalUsers);
    echo '$externalId: ' . $externalId . PHP_EOL;
    //

} catch (BrazeException $e) {
    echo 'BrazeException from: ' . $e::class . PHP_EOL;
    echo 'Message: ' . $e->getMessage() . PHP_EOL;
    echo 'Full response: ' . $e->getFullResponse() . PHP_EOL;
    var_dump($e->getBody());
}
