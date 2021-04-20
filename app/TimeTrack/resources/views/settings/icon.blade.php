<div class="col-md-2 col-sm-6">
    <div class="settingiconblue">
        <div class="settingdivblue">
            <a href="{{ route('timetrack.setting') }}" onclick="sidebaropen(this)">
                <span class="fa-stack fa-2x">
                    <i class="fas fa-clock fa-stack-1x">
                    </i>
                </span>
            </a>
        </div>
        <div class="text-center text-sm">
            {{ Lang::get('timetrack::lang.time-track') }}
        </div>
    </div>
</div>
