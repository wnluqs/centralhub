@extends('layouts.app')

@section('content')
    <div id="content">

        <div class="overlay">
            <h4 class="text-black mt-3" id="greeting">
                Hi, {{ Auth::user()->name }}! 👋
            </h4>
            <p style="color: grey;" id="datetime"></p>

            @push('scripts')
                <script>
                    function updateGreeting() {
                        const now = new Date();
                        const hour = now.getHours();
                        let greetingTime = "Hello";

                        if (hour < 12) {
                            greetingTime = "Good Morning";
                        } else if (hour < 18) {
                            greetingTime = "Good Afternoon";
                        } else {
                            greetingTime = "Good Evening";
                        }

                        const userName = @json(Auth::user()->name);
                        document.getElementById('greeting').innerHTML = `${greetingTime}, ${userName}! 👋`;
                    }

                    function updateDateTime() {
                        const now = new Date();
                        const options = {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        };
                        const time = now.toLocaleTimeString();
                        const date = now.toLocaleDateString(undefined, options);
                        document.getElementById('datetime').innerHTML = `📅 ${date} | 🕒 ${time}`;
                    }

                    setInterval(updateDateTime, 1000);
                    updateGreeting();
                    updateDateTime();
                </script>
            @endpush

            <h2 class="text-black">Internal System Website</h2>
            <p style="color: grey">Hello, welcome to Vista Summerose Internal Website.</p>
            <p style="color: grey">You can see here we have multiple departments in this organization.</p>

            {{-- Smart Recommendations --}}
            <div class="recommendation-box mt-3 p-3" style="background: #f8f9fa; border-radius: 12px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
                <h5 class="text-primary">🧠 Smart Recommendations</h5>
                <ul style="list-style: none; padding-left: 0; margin-bottom: 0;">
                    @php
                        use App\Models\Complaint;
                        use App\Models\Inspection;
                        use Carbon\Carbon;

                        $today = Carbon::now()->toDateString();
                        $user = Auth::user();
                        $hasInspectionToday = Inspection::where('submitted_by', $user->name)
                                                        ->whereDate('created_at', $today)
                                                        ->exists();
                        $assignedComplaints = Complaint::where('assigned_to', $user->id)
                                                       ->where('status', 'In Progress')
                                                       ->count();
                    @endphp

                    @if (!$hasInspectionToday)
                        <li>📝 You haven’t submitted any inspection today.</li>
                    @endif

                    @if ($assignedComplaints > 0)
                        <li>📋 You have {{ $assignedComplaints }} complaint(s) in progress.</li>
                    @endif

                    @if ($hasInspectionToday && $assignedComplaints === 0)
                        <li>✅ All caught up! No pending actions right now.</li>
                    @endif
                </ul>
            </div>

            <div class="department-icons mt-4">
                {{-- HR Icon: Only show if user can access HR department --}}
                <a href="{{ route('departments.hr') }}" class="icon-link">
                    <img src="{{ asset('images/traffic.jpg') }}" alt="Traffic Management">
                    <span>Traffic Management</span>
                </a>

                {{-- Accounting Icon --}}
                <a href="{{ route('departments.accounting') }}" class="icon-link">
                    <img src="{{ asset('images/citycarpark.png') }}" alt="CityCarPark App">
                    <span>CityCarPark App</span>
                </a>

                {{-- Operations Icon --}}
                <a href="{{ route('departments.operations') }}" class="icon-link">
                    <img src="{{ asset('images/erp.jpg') }}" alt="ERP">
                    <span>HandHeld Manager</span>
                </a>
            </div>
        </div>
    </div>
@endsection
