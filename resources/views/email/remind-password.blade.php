<!DOCTYPE html>
<html>
<body>
<h3>以下のURLに仮パスワードを入力して、個人パスワードの再設定をお願いします。</h3>
<p>URL: <a href="{{URL::to('/login')}}">{{URL::to('/login')}}</a></p>
<p>社員番号：{{ $detail['emp_code'] }}</p>
<p>仮パスワード：{{ $detail['password'] }}</p>
<p>イズミ物流　労務Team</p>
</body>
</html>
