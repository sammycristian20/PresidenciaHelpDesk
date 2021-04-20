<div>


@if(Auth::user()->role == 'admin')
    
    <div id="header-container">

        <system-updates :updates-count="{{ json_encode($notification->count()) }}" ></system-updates>
    </div>
@endif

</div>

<style>
    .auto-update-blink {
        display:inline-block;
        animation: blink 2s ease-in infinite;
    }

    @keyframes blink {
        from, to { opacity: 1 }
        50% { opacity: 0 }
    }
</style>