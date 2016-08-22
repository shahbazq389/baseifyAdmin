<?php

namespace App\Http\Controllers;
use view;
use Illuminate\Http\Request;

use App\Http\Requests;
use DB;
use PDF;
use Carbon\Carbon;
use Excel;
use App\Http\Controllers\userdata;
use Session;
use Illuminate\Support\Facades\Redirect;
class DatatableController extends Controller
{
    public function index(){
        return view('datatable.index');
    }

    public function show1(Request $request)
    {
        // DB columns array
        $columns=array(
            "",
            "id",

            "name",
            "advertiser_types.type",
            "",
            "created_at"
        );

        // local variables for POST variables for searching columns
        $advertiser_id="";
        $advertiser_name="";
        $advertiser_type_id="";
        $advertiser_widget_string="";
        $advertiser_created_from="";
        $advertiser_created_to="";

        // Assigning POST values to local variables

        if($request->has('advertiser_id') && $request->get('advertiser_id')!=null)
            $advertiser_id=trim($request->get('advertiser_id'));

        if($request->has('advertiser_name') && $request->get('advertiser_name')!=null)
            $advertiser_name=trim($request->get('advertiser_name'));

        if($request->has('advertiser_type') && $request->get('advertiser_type')!=null)
            $advertiser_type_id=trim($request->get('advertiser_type'));

        if($request->has('advertiser_widgets') && $request->get('advertiser_widgets')!=null)
            $advertiser_widget_string=trim($request->get('advertiser_widgets'));


        if($request->has('advertiser_created_from') && $request->get('advertiser_created_from')!=null) {
            $advertiser_created_from = $request->get('advertiser_created_from');
            $advertiser_created_from_obj = DateHelper::dateStringToCarbon($advertiser_created_from ,'d/m/Y');
            $advertiser_created_from =$advertiser_created_from_obj->format('Y-m-d 00:00:00');
        }

        if($request->has('advertiser_created_to') && $request->get('advertiser_created_to')!=null) {
            $advertiser_created_to = $request->get('advertiser_created_to');
            $advertiser_created_to_obj = DateHelper::dateStringToCarbon($advertiser_created_to ,'d/m/Y');
            $advertiser_created_to =$advertiser_created_to_obj->format('Y-m-d 23:59:59');
        }

        $iDisplayLength = intval($request->get('length'));  // getting rows per page value for paging
        $iDisplayStart = intval($request->get('start'));    // getting offset value for paging
        $sEcho = intval($request->get('draw'));

        $query_order_array=$request->get('order', array(array('column'=>1,'dir'=>'asc')));
        $query_order_column=$query_order_array[0]['column'];
        $query_order_direction=$query_order_array[0]['dir'];

        // Building query for search
        $query = DB::table('advertisers');
        $query->leftjoin('advertiser_types','advertiser_types.id','=','advertisers.type_id');
        $query->leftjoin('advertiser_widgets_advertisers','advertiser_widgets_advertisers.advertiser_id','=','advertisers.id');
        $query->leftjoin('advertiser_widgets','advertiser_widgets.id','=','advertiser_widgets_advertisers.advertiser_widget_id');
        $query->select(DB::raw("GROUP_CONCAT(DISTINCT advertiser_widgets.name ORDER BY advertiser_widgets.name SEPARATOR ', ') as advertiserwidgets"),'advertisers.*', 'advertiser_types.type as advertiser_type');
        //$query->select('advertisers.*', 'advertiser_types.type as advertiser_type');
        $query->where('advertisers.is_delete','=',0);

        /*$tags[]=1;
        $tags[]=2;
        $query->whereHas('advertiser_widgets', function($query) use($tags) {
            $query->whereIn('name', $tags);
        });
        */

        if($advertiser_id!=null)
            $query->where('advertisers.id','=',$advertiser_id);

        if($advertiser_name!=null)
            $query->where('advertisers.name','LIKE','%'.$advertiser_name.'%');

        if($advertiser_type_id!=null)
            $query->where('advertisers.type_id','=',$advertiser_type_id);

        if($advertiser_created_from!=null)
            $query->where('advertisers.created_at','>=',$advertiser_created_from);

        if($advertiser_created_to!=null)
            $query->where('advertisers.created_at','<=',$advertiser_created_to);


        $query->groupBy('advertisers.id');
        if($advertiser_widget_string!=null)
            $query->havingRaw("GROUP_CONCAT(DISTINCT advertiser_widgets.name ORDER BY advertiser_widgets.name SEPARATOR ', ') LIKE '%".$advertiser_widget_string."%'");

        // copying query for total records
        //$copy_query = $query;
        //$iTotalRecords=$copy_query->count();

        $sql=$query->toSql();

        $count = DB::table( DB::raw("($sql) as sub") )
            ->mergeBindings($query) // you need to get underlying Query Builder
            ->count();

        $iTotalRecords=$count;

        //$iTotalRecords= DB::table(DB::raw("($sql) AS a"))->count();

        $query->orderBy($columns[$query_order_column], $query_order_direction);

        if($iDisplayLength>0)
            $query->limit($iDisplayLength)->offset($iDisplayStart);

        //getting searched records
        $advertisers=$query->get();

        $i=0;
        $records = array();
        $records["data"] = array();
        foreach($advertisers as $advertiser)
        {
            $advertiserwidgets=$advertiser->advertiserwidgets;
            if(trim($advertiserwidgets)=="")
                $advertiserwidgets="N/A";

            $advertisertype=$advertiser->advertiser_type;
            if(trim($advertisertype)=="")
                $advertisertype="N/A";


            $advertiser->created_at =  Carbon::parse($advertiser->created_at);
            $records['data'][$i][]='<input type="checkbox" name="id[]" value="'.$advertiser->id.'">';
            $records['data'][$i][]=$advertiser->id;
            $records['data'][$i][]=$advertiser->name;
            $records['data'][$i][]=$advertisertype;
            $records['data'][$i][]=$advertiserwidgets;
            $records['data'][$i][]=$advertiser->created_at->format('m-d-Y h:i A');
            $records['data'][$i][]='
                <div class="btn-group" role="group">
                    <a href="'.url('/edit', [$advertiser->id]).'" class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i></a>
                    <a href="'.url('/delete', [$advertiser->id]).'" class="btn btn-sm btn-danger"><i class="fa fa-times"></i></a>
                </div>';
            $i++;
        }
        if ($request->get("customActionType")!==null && $request->get("customActionType") == "group_action") {
            $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
            $records["customActionMessage"] = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        return $records;
    }

    public function csv_Export(Request $request)
    {   //echo "it works";

        $id = $request->get('selected_id');
        $id = explode(',',$id);

        //print_r($id);die();
        $userdata=DB::table('advertisers')->whereIn('id',$id)->get();
        /*echo '<pre>';
        print_r($userdata);
        echo '------------------------<br/>';*/
        $userdata = json_decode( json_encode($userdata), true);
        /*print_r($userdata); die();
        echo '</pre>';*/

        /*  echo '<pre>';
        print_r(array(
            array('id'=>'data1', 'name'=>'data2'),
            array('id'=>'data3', 'name'=>'data4')
        ));
        echo '------------------------<br/>';
        print_r($userdata);
        echo '</pre>';
       // die();*/



        Excel::create('Laravel_Excel', function($excel) use($userdata) {

            $excel->sheet('Excel sheet', function($sheet) use($userdata) {

                $sheet->fromArray($userdata);
                /*$sheet->fromArray(array(
                    array('id'=>'data1', 'name'=>'data2'),
                    array('id'=>'data3', 'name'=>'data4')
                ));*/

            });

        })->export('csv');


        //return redirect()->back();
    }

    /**
     * download PDF-generating
     * @param Request $request
     * @return mixed
     */
    public function pdf_Export(Request $request)
    {   /*echo "it works";
        die();*/
        $id = $request->get('selected_id');
        $id = explode(',', $id);

        //print_r($id);die();
        $userdata = DB::table('advertisers')->whereIn('id', $id)->get();
        /*echo '<pre>';
        print_r($userdata);
        echo '------------------------<br/>';*/
        /*$userdata = json_decode(json_encode($userdata), true);
        /*print_r($userdata); die();
        echo '</pre>';*/
        $pdf = PDF::loadView('pdf', ['userdata'=> $userdata]);
        return $pdf->download('laravel.pdf');
    }

        public function delete($id)
    {
        //echo $id;
        $del = DB::table('advertisers')->where('id',$id)->delete();
        if($del){
          //  \session::flash('message','Deleted Succesfully');
            return redirect::to('datatable')->with('deletemsg', 'Data Deleted Succesfully !!');
        }
    }

    public function edit($id){
        //echo $id;
        $edit_data=DB::table('advertisers')->where('id',$id)->get();
        return view('datatable.edit',compact('edit_data',$edit_data));
    }

    public function update(Request $request){
        $input=$request->all();
       // print_r($input);die();
        $data=array('name'=>$input['name'],'created_at'=>$input['Updatedate']);
        $upd=DB::table('advertisers')->where('id',$input['id'])->update($data);
        if($upd){
            return redirect::to('datatable')->with('updatemsg','Data Updated Succesfully !!');
        }
    }
}
