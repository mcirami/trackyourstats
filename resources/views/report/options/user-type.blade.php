<script type="text/javascript">
    function handleSelect(elm) {
        window.location = "/<?=request()->path() . '?' . http_build_query(request()->except(['role']))?>&role=" + elm.value;
    }
</script>

<select onchange="handleSelect(this);" class="selectBox " style="width:135px;" id="role" name="role">


    @if(\LeadMax\TrackYourStats\System\Session::userType() == \App\Privilege::ROLE_GOD)
        <option @if(request('role',3) == 1) selected @endif value='1'>Admins
        </option>
    @endif


    @if(\LeadMax\TrackYourStats\System\Session::permissions()->can("create_managers"))
        <option @if(request('role',3) == 2) selected @endif value='2'>Managers</option>
    @endif
    <option @if(request('role',3 ) == 3) selected @endif value='3'>Affiliates</option>
</select>
