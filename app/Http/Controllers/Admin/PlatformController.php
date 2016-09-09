<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Api\LogDetails;
use Illuminate\Pagination\LengthAwarePaginator;

class PlatformController extends Controller
{
    protected $header = [
        'channel'   => '渠道',
        'all'       => '全部',
        'debug'     => '调试',
        'info'      => '信息',
        'notice'    => '注意',
        'warning'   => '警告',
        'error'     => '错误',
        'critical'  => '严重',
        'alert'     => '紧急',
        'emergency' => '危急',
    ];

    protected $perPage = 30;

    public function __construct()
    {
        $this->perPage = config('log-viewer.per-page', $this->perPage);
    }
    /**
     * 平台日志首页
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $detail = LogDetails::raw()->aggregate([
            [
                '$group' => [
                    '_id' => '$level_name',
                    'total' => ['$sum' => 1]
                ]
            ]
        ])->toArray();
        $detail = json_decode(json_encode($detail), true);
        $detail = array_column($detail, 'total', '_id');
        $detail['all'] = LogDetails::count();

        $footer = [];
        foreach($this->header as $k => $v) {
            if ($k == 'channel') {
                continue;
            } else {
                $footer[$k] = isset($detail[$k]) ? $detail[$k] : 0;
            }
        }
        $percents = $this->calcPercentages($footer, $this->header);

        $levels = array_except($footer, 'all');
        $json = [];
        foreach ($levels as $level => $count) {
            $json[] = [
                'label'     => "PolyLog::levels." . $level,
                'value'     => $count,
                'color'     => config("log-viewer.colors.levels." . $level),
                'highlight' => config("log-viewer.colors.levels." . $level),
            ];
        }
        $reports = json_encode(array_values($json), JSON_PRETTY_PRINT);

        return view('admin.platform.index', compact('reports', 'percents'));
    }

    /**
     * 各平台日志列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function listLogs(Request $request)
    {
        $page = $request->input('page', 1);
        $offset  = ($page * $this->perPage) - $this->perPage;
        $headers = $this->header;
        $details = LogDetails::raw()->aggregate([
            [
                '$group' => [
                    '_id' => '$channel',
                    'all' => ['$sum' => 1],
                    'debug' => [
                        '$sum' => [
                            '$cond' => [
                                ['$eq' => ['$level_name', 'debug']],
                                1,
                                0
                            ]
                        ]
                    ],
                    'info' => [
                        '$sum' => [
                            '$cond' => [
                                ['$eq' => ['$level_name', 'info']],
                                1,
                                0
                            ]
                        ]
                    ],
                    'notice' => [
                        '$sum' => [
                            '$cond' => [
                                ['$eq' => ['$level_name', 'notice']],
                                1,
                                0
                            ]
                        ]
                    ],
                    'warning' => [
                        '$sum' => [
                            '$cond' => [
                                ['$eq' => ['$level_name', 'warning']],
                                1,
                                0
                            ]
                        ]
                    ],
                    'error' => [
                        '$sum' => [
                            '$cond' => [
                                ['$eq' => ['$level_name', 'error']],
                                1,
                                0
                            ]
                        ]
                    ],
                    'critical' => [
                        '$sum' => [
                            '$cond' => [
                                ['$eq' => ['$level_name', 'critical']],
                                1,
                                0
                            ]
                        ]
                    ],
                    'alert' => [
                        '$sum' => [
                            '$cond' => [
                                ['$eq' => ['$level_name', 'alert']],
                                1,
                                0
                            ]
                        ]
                    ],
                    'emergency' => [
                        '$sum' => [
                            '$cond' => [
                                ['$eq' => ['$level_name', 'emergency']],
                                1,
                                0
                            ]
                        ]
                    ],
                ]
            ]
        ])->toArray();
        $details = json_decode(json_encode($details), true);
        $logs = [];
        foreach ($details as $v) {
            $v = array_prepend($v, $v['_id'], 'channel');
            unset($v['_id']);
            $logs[$v['channel']] = $v;
        }
        $rows = new LengthAwarePaginator(
            array_slice($logs, $offset, $this->perPage, true),
            count($logs),
            $this->perPage,
            $page
        );
        $rows->setPath($request->url());

        return view('admin.platform.logs', compact('headers', 'rows'));
    }

    /**
     * 删除
     * @param Request $request
     * @param LogDetails $logDetails
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, LogDetails $logDetails)
    {
        if ( ! $request->ajax()) abort(405, '非法请求');

        $channel = $request->get('channel');
        $ajax = [
            'result' => $logDetails->delLogs($channel) ? 'success' : 'error'
        ];

        return response()->json($ajax);
    }

    /**
     * Calculate the percentage
     *
     * @param  array  $total
     * @param  array  $names
     *
     * @return array
     */
    private function calcPercentages(array $total, array $names)
    {
        $percents = [];
        $all      = array_get($total, 'all');

        foreach ($total as $level => $count) {
            $percents[$level] = [
                'name'    => $names[$level],
                'count'   => $count,
                'percent' => $all ? round(($count / $all) * 100, 2) : 0,
            ];
        }

        return $percents;
    }
}
