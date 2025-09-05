<?php

use Illuminate\Support\Facades\Lang;

// В обработчике вебхука добавьте заголовок
// $_SERVER['HTTP_XDEBUG_SESSION'] = 'PHPSTORM';

use App\Telegram\SalesBot\TelegramSalesBot;
use App\Services\HuntflowApplicantService;

require __DIR__ . '/app/bootstrap/helpers.php';
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/app/bootstrap/redis.php';
require __DIR__ . '/app/bootstrap/db.php';
require __DIR__ . '/app/bootstrap/app.php';

$test = Lang::get('messages.apples');
$test2 = trans('messages.apples');

// new TelegramSalesBot();

//while (true) {
//    $huntflowService = new HuntflowApplicantService();
//
//    $applicantData = [
//        'last_name' => 'Петров',
//        'first_name' => 'Алексей',
//        'phone' => '+79167778899',
//        'email' => 'petrov@example.com',
//        'position' => 'Backend Developer',
//    ];
//
//    $result = $huntflowService->createApplicant($applicantData);
//
//    if ($result['success']) {
//
//        $result2 = $huntflowService->addApplicationToVacancy(
//            $result['applicant_id'],
//            '4055147',
//            '129782',
//        // $applicationData
//        );
//
//        if (!$result2['success']) {
//            echo 'Applicant created fail';
//        } else {
//            echo 'Applicant created success';
//        }
//    }
//}

//$result = $huntflowService->addApplicationToVacancy(
//    '73522573',
//    '4055147',
//    '129782',
//    // $applicationData
//);
//
//if (!$result['success']) {
//    echo 'Applicant created fail';
//} else {
//    echo 'Applicant created success';
//}

//$result = $huntflowService->createApplicant($applicantData);
//
//if (!$result['success']) {
//    echo 'Applicant created fail';
//} else {
//    echo 'Applicant created success';
//}

//$result = $huntflowService->getVacancyStatuses();
//
//if (!$result['success']) {
//    echo 'fail';
//} else {
//    echo json_encode($result['data'], JSON_UNESCAPED_UNICODE);
//}
