<!-- postulate Modal -->
<link rel="stylesheet" href="{{asset('assets/admin/css/summernote-bs4.css')}}">

<div class="modal fade" id="postulateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">{{__('Postulate')}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">

            <form id="applyForm" name="applyForm" enctype="multipart/form-data" class="modal-form" action="{{route('front.job.apply')}}" method="POST">
               @csrf
               <div class="row">
                  <div class="col-lg-6">
                    <div class="form-group">
                      <div class="col-12 mb-2">
                        <i class="fas fa-file-upload"></i> <label for="cv"><strong>{{__('CV')}}</strong></label>
                      </div>
                      <input type="file" name="cv" id="cv" class="form-control">
                      <p id="errcv" class="mb-0 text-danger em"></p>
                    </div>
                  </div>
                </div>
                <input type="hidden" name="job_slug" value="{{$job->slug}}">
                <div class="form-group">
                  <label for="">{{__('Full Name')}} **</label>
                  <input type="text" class="form-control" name="full_name" placeholder="{{__('Full Name')}}" value="">
                  <p id="errfull_name" class="mb-0 text-danger em"></p>
               </div>
               <div class="form-group">
                  <label for="">{{__('Subject')}} **</label>
                  <input type="text" class="form-control" name="title" placeholder="{{__('Subject')}}" value="">
                  <p id="errtitle" class="mb-0 text-danger em"></p>
               </div>
               <div class="form-group">
                  <label for="">{{__('Write a message')}} **</label>
                  <textarea class="form-control" name="content" rows="8" cols="80" placeholder="{{__('Write a message')}}..."></textarea>
                  <p id="errcontent" class="mb-0 text-danger em"></p>
               </div>
            </form>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
            <button id="submitBtn" type="button" form="applyForm" type="button" class="btn btn-primary">{{__('Submit')}}</button>
         </div>
      </div>
   </div>

   <script>
    $('#submitBtn').on('click', function(e) {
    $(e.target).attr('disabled', true);
        
        $(".request-loader").addClass("show");
        $("#applyForm").attr('onsubmit', 'return false');
    let ajaxForm = document.getElementById('applyForm');
    let fd = new FormData(ajaxForm);
    let url = $("#applyForm").attr('action');
    let method = $("#applyForm").attr('method');
    $.ajax({
            url: url,
            method: method,
            data: fd,
            contentType: false,
            processData: false,
            success: function(data) {
                console.log(data);
                $(e.target).attr('disabled', false);
                $(".request-loader").removeClass("show");
                
                $(".em").each(function() {
                    $(this).html('');
                })
                
                if (data == "success") {
                    location.reload();
                }
                
                // if error occurs
                else if (typeof data.error != 'undefined') {
                    for (let x in data) {
                        if (x == 'error') {
                            continue;
                        }
                        document.getElementById('err' + x).innerHTML = data[x][0];
                    }
                }
            }
        });
    })
   </script>
    
</div>
