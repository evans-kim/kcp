#KCP 결제 API

라라벨을 위한 KCP 결제 API 입니다. 개인적으로 작업한 내용으로 완전한 작동을 보장하지 않으니 
반드시 코드를 살피시고 적합하게 커스터마이즈 하세요.

##설치

    php artisan vendor:publish --tag=kcp_payment
    
    php artisan migrate
    
## 설정

~config/service.php 에 아래의 설정값 추가
설정되어 있지 않으면 자동 테스트 모드로 실행됩니다. 

    'kcp'=>[
        'site_code'=>'*****',
        'site_key'=>'**********************',
        'site_name'=>'사이트명'
    ]

## 커스터마이즈해야할 페이지

- src/KCP/views/form.blade.php
- src/KCP/views/mobile-form.blade.php
- src/KCP/KcpController.php

## 문의사항

evans@kakao.com
