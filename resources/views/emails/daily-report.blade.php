@component('mail::message')
    # Daily CCTV System Report

    Here's a summary of your CCTV system activity for the past 24 hours:

    @component('mail::panel')
        ## Cameras
        - **Total Cameras:** {{ $stats['total_cameras'] }}
        - **Online Cameras:** {{ $stats['online_cameras'] }}

        ## Alerts
        - **Total Alerts:** {{ $stats['total_alerts'] }}
        - **Unknown Faces Detected:** {{ $stats['unknown_faces'] }}

        ## Recordings
        - **Videos Recorded:** {{ $stats['recorded_videos'] }}

        ## System Status
        - **Uptime:** {{ $stats['system_uptime'] }}
    @endcomponent

    @component('mail::button', ['url' => url('/dashboard')])
        View Dashboard
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
