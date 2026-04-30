<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Holiday;
use App\Models\LeaveBalance;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use jcobhams\NewsApi\NewsApi;
use App\Services\EsslService;
use App\Models\Employee;
use App\Models\Vendor;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    protected $esslService;
    protected $q;
    protected $sources;
    protected $country;
    protected $category;
    protected $page_size;
    protected $page;

    public function __construct(EsslService $esslService)
    {
        $this->esslService = $esslService;
        $this->q = null;
        $this->sources = null;
        $this->country = 'in';
        $this->category = null;
        $this->page_size = 10;
        $this->page = 1;
        $this->domains = null;
        $this->exclude_domains = null;
        $this->from = now()->subWeek(); // last week date
        $this->to = now(); // today date
        $this->language = null;
        $this->sort_by = null;
    }

    //
    public function index(Request $request)
    {
        if(Auth::user()->hasRole('Employee')) {
            $employee = Employee::where('employee_email', Auth::user()->email)->first();
            // if($employee->bgv == false ){
            //     return redirect()->route('bgv.index');
            // }else
            // if($employee->induction == false){
            //     return redirect()->route('induction.candidate.portal');
            // }
        }
        elseif (Auth::user()->hasRole('reception')) {
            return redirect()->route('visitors.index');
        }elseif(Auth::user()->hasRole('Vendor')) {
            $vendor = Vendor::where('email', Auth::user()->email)->first();
            if(!$vendor) {
                return redirect()->route('vendor.verify.pending');
            }
            if($vendor->status == 'pending_verification') {
                return redirect()->route('vendor.verify.pending');
            }elseif($vendor->status == 'blocked') {
                return redirect()->route('vendor.verify.blocked');
            }else{
                return redirect()->route('vendor.dashboard');
            }
        }

        //$role = Auth::user()->roles->pluck('name')[0];
        $role = Auth::user()->roles->pluck('name')[0] ?? 'employee';
        $user = Auth::user();
        $sort = $request->get('sort', 'most_recent');

        $posts = Post::with(['user', 'likes', 'comments'])
                    ->when($sort === 'trending', fn($query) => $query->trending())
                    ->when($sort === 'most_liked', fn($query) => $query->withCount('likes')->orderBy('likes_count', 'desc'))
                    ->when($sort === 'most_recent', fn($query) => $query->mostRecent())
                    ->get();

        $top_headlines = array();

        $newsapi = new NewsApi(config('services.newsapi.key'));
        $top_headlines = $newsapi->getTopHeadlines($this->q, $this->sources, $this->country, $this->category, $this->page, $this->page_size);
        #$top_headlines = $newsapi->getEverything($this->q, $this->sources, $this->domains, $this->exclude_domains, $this->from, $this->to, $this->language, $this->sort_by,  $this->page_size, $this->page);
        #$top_headlines = $top_headlines->articles;
        $isServingNotice = false;

        // Get attendance stats for employee dashboard
        $dashboardStats = [];
        if (in_array(strtolower($role), ['employee', 'tech'])) {
            $attendanceController = new AttendanceController($this->esslService);
            $dashboardStats = $attendanceController->getDashboardStats($request);
            
            // Check if employee is serving notice
            $isServingNotice = $request->employee && $request->employee->resignation && 
                              $request->employee->resignation->status === 'approved' && 
                              $request->employee->resignation->last_working_day > now();
        }

        // Get leave balance for the current user
        $totalLeaveBalance = LeaveBalance::where('user_id', Auth::id())
            ->sum('balance');

        // Get upcoming holidays
        $upcomingHolidays = Holiday::where('date', '>=', now())
            ->orderBy('date')
            ->take(1)
            ->get();

        // Get today's WFH employees
        $wfhEmployees = Attendance::with('user:id,name,department')
            ->whereDate('date', Carbon::today())
            ->where('work_type', 'wfh')
            ->whereNotNull('first_in')
            ->get()
            ->map(function ($attendance) {
                return $attendance->user;
            });

        switch(strtolower($role)) {
            case 'superadmin':
                return view('dashboard.admin-dashboard', compact('posts', 'totalLeaveBalance', 'upcomingHolidays', 'wfhEmployees'));
            case 'hr':
                return view('dashboard.hr-dashboard', compact('top_headlines', 'posts', 'totalLeaveBalance', 'upcomingHolidays', 'wfhEmployees'));
            case 'manager':
                return view('dashboard.manager-dashboard', compact('top_headlines', 'posts', 'totalLeaveBalance', 'upcomingHolidays', 'wfhEmployees'));
            case 'finance':
                return view('dashboard.finance-dashboard', compact('posts', 'totalLeaveBalance', 'upcomingHolidays', 'wfhEmployees'));
            case 'employee':
                return view('dashboard.employee-dashboard', compact('isServingNotice', 'top_headlines', 'posts', 'dashboardStats', 'totalLeaveBalance', 'upcomingHolidays', 'wfhEmployees'));
            case 'tech':
                return view('dashboard.tech-dashboard', compact('isServingNotice', 'top_headlines', 'posts', 'dashboardStats', 'totalLeaveBalance', 'upcomingHolidays', 'wfhEmployees'));
            default:
                return view('dashboard.employee-dashboard', compact('isServingNotice', 'top_headlines', 'posts', 'dashboardStats', 'totalLeaveBalance', 'upcomingHolidays', 'wfhEmployees'));
        }
    }

    function getLinkedInPosts($accessToken, $organizationId = null)
    {
        $url = $organizationId 
            ? "https://api.linkedin.com/v2/shares?q=organizationOwner&owners=urn:li:organization:$organizationId" 
            : "https://api.linkedin.com/v2/shares?q=authors&authors=urn:li:person:ee4eac36-0a2b-47e1-9fbe-21dfa073971d";

        $response = Http::withHeaders([
            'Authorization' => "Bearer $accessToken",
            'X-Restli-Protocol-Version' => '2.0.0',
        ])->get($url);

        return $response->json();
    }


    public function orgChart()
    {
        return view('org-chart');
    }
}
