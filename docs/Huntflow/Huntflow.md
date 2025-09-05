# Huntflow

## Тестовая вакансия

https://huntflow.ru/my/softgamings#/vacancy/4055147

## Добавление applicant

```
$applicantData = [
'last_name' => 'Петров',
'first_name' => 'Алексей',
'phone' => '+79167778899',
'email' => 'petrov@example.com',
'position' => 'Backend Developer',
];

{"first_name":"\u0410\u043b\u0435\u043a\u0441\u0435\u0439","last_name":"\u041f\u0435\u0442\u0440\u043e\u0432","middle_name":null,"money":null,"phone":"+79167778899","email":"petrov@example.com","skype":null,"position":"Backend Developer","company":null,"photo":null,"id":73522573,"created":"2025-09-05T11:28:40+03:00","birthday":null,"files":null,"doubles":[{"double":39545133}],"agreement":null,"external":[{"id":73351472,"auth_type":"NATIVE","account_source":null,"updated":"2025-09-05T11:28:40+03:00"}],"social":[],"reindex_job_id":"d1641fba-7b30-441b-8ba9-1786f54921b8"}

{"first_name":"\u0410\u043b\u0435\u043a\u0441\u0435\u0439","last_name":"\u041f\u0435\u0442\u0440\u043e\u0432","middle_name":null,"money":null,"phone":"+79167778899","email":"petrov@example.com","skype":null,"position":"Backend Developer","company":null,"photo":null,"id":73522816,"created":"2025-09-05T11:31:57+03:00","birthday":null,"files":null,"doubles":[{"double":39545133},{"double":73522573}],"agreement":null,"external":[{"id":73351715,"auth_type":"NATIVE","account_source":null,"updated":"2025-09-05T11:31:57+03:00"}],"social":[],"reindex_job_id":"29f7294c-d340-41a7-8738-09dfafcaf676"}
```

## Цикл добавления откликов

```
<?php

use Illuminate\Support\Facades\Lang;

while (true) {
    $huntflowService = new HuntflowApplicantService();

    $applicantData = [
        'last_name' => 'Петров',
        'first_name' => 'Алексей',
        'phone' => '+79167778899',
        'email' => 'petrov@example.com',
        'position' => 'Backend Developer',
    ];

    $result = $huntflowService->createApplicant($applicantData);

    if ($result['success']) {

        $result2 = $huntflowService->addApplicationToVacancy(
            $result['applicant_id'],
            '4055147',
            '129782',
        // $applicationData
        );

        if (!$result2['success']) {
            echo 'Applicant created fail';
        } else {
            echo 'Applicant created success';
        }
    }
}
```
