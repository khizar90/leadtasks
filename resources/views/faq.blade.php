@extends('layouts1.base')
@section('title', 'Faq')
@section('main', 'Faqs Management')
@section('link')
    <link rel="stylesheet" href="/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Users List Table -->
            <div class="card">

                <div class="card-datatable table-responsive">
                    <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissible" role="alert">
                                {{ session()->get('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        @if (session()->has('edit'))
                            <div class="alert alert-success alert-dismissible" role="alert">
                                {{ session()->get('edit') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                        @if (session()->has('delete'))
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                {{ session()->get('delete') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="row me-2 mt-4 mb-2">
                            <div class="col-md-2">
                                <div class="me-3">

                                    <div class="dataTables_length" id="DataTables_Table_0_length"><label
                                            style="" class="fw-bold">
                                            Frequently Asked
                                            Question
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div
                                    class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">

                                    <div class="dt-buttons btn-group flex-wrap">
                                        <button class="btn btn-secondary add-new btn-primary" tabindex="0"
                                            aria-controls="DataTables_Table_0" type="button" data-bs-toggle="modal"
                                            data-bs-target="#addNewBus"><span><i
                                                    class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span
                                                    class="d-none d-sm-inline-block">Add Frequently Asked
                                                    Question</span></span></button>
                                    </div>



                                </div>
                            </div>

                        </div>



                        <hr class="mb-3">

                        <div class="row">
                            <div id="accordionPayment" class="accordion">
                                <div class="row">
                                    @foreach ($faqs as $faq)
                                        <div class="col-md-11 mb-2">

                                            <div class="card accordion-item">

                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#accordionPayment-{{ $faq->id }}"
                                                        aria-controls="accordionPayment-2">
                                                        {{ $faq->question }}
                                                    </button>
                                                </h2>
                                                <div id="accordionPayment-{{ $faq->id }}"
                                                    class="accordion-collapse collapse">
                                                    <div class="accordion-body">
                                                        {{ $faq->answer }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1 d-flex align-items-center justify-content-center">
                                            <a data-bs-toggle="modal" data-bs-target="#edit{{ $faq->id }}"
                                                class="delete-icon">
                                                <i class="bi bi-pencil-square mx-2"></i>
                                            </a>
                                            <a data-bs-toggle="modal" data-bs-target="#deleteModal{{ $faq->id }}"
                                                class="delete-icon">
                                                <i class="bi bi-trash mx-2"></i>
                                            </a>
                                            <div class="modal fade"data-bs-backdrop='static'  id="deleteModal{{ $faq->id }}" tabindex="-1"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                                    <div class="modal-content deleteModal verifymodal">
                                                        <div class="modal-header">
                                                            <div class="modal-title" id="modalCenterTitle">
                                                                Are you sure you want to delete
                                                                this FAQ's?
                                                            </div>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="body">
                                                                After deleting the FAQ's you will add a new
                                                                FAQ's
                                                            </div>
                                                        </div>
                                                        <hr class="hr">

                                                        <div class="container">
                                                            <div class="row">
                                                                <div class="first">
                                                                    <a href="" class="btn" data-bs-dismiss="modal"
                                                                        style="color: #a8aaae ">Cancel</a>
                                                                </div>
                                                                <div class="second">
                                                                    <a class="btn text-center"
                                                                        href="{{ route('dashboard-faqs-delete', $faq->id) }}">Delete</a>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade" data-bs-backdrop='static' id="edit{{ $faq->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="modalCenterTitle">Edit faq</h5>
                            
                                                        </div>
                            
                                                        <form action="{{ route('dashboard-faqs-edit' ,$faq->id) }}" id="" method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col mb-3">
                                                                        <label for="nameWithTitle" class="form-label">Question</label>
                                                                        <input type="text" id="nameWithTitle" name="question"
                                                                            class="form-control" value="{{ $faq->question }}" placeholder="Question" required />
                            
                                                                    </div>
                            
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col mb-3">
                                                                        <label for="nameWithTitle" class="form-label">Answer</label>
                                                                        <textarea id="" name="answer"  class="form-control" rows="3" placeholder="Answer" required> {{ $faq->answer }}</textarea>
                                                                    </div>
                            
                                                                </div>
                            
                            
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-label-secondary" id="closeButton" data-bs-dismiss="modal">
                                                                    Close
                                                                </button>
                                                                <button type="submit" class="btn btn-primary">Edit FAQ</button>
                                                            </div>
                                                        </form>
                            
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                </div>


                            </div>

                        </div>








                    </div>
                </div>
                <!-- Offcanvas to add new user -->



                <div class="modal fade" data-bs-backdrop='static' id="addNewBus" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalCenterTitle">Add New Faq</h5>

                            </div>

                            <form action="{{ route('dashboard-faqs-add') }}" id="addBusForm" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="nameWithTitle" class="form-label">Question</label>
                                            <input type="text" id="nameWithTitle" name="question"
                                                class="form-control" placeholder="Question" required />

                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="nameWithTitle" class="form-label">Answer</label>
                                            <textarea id="" name="answer" class="form-control" rows="3" placeholder="Answer" required></textarea>
                                        </div>

                                    </div>


                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-label-secondary" id="closeButton" data-bs-dismiss="modal">
                                        Close
                                    </button>
                                    <button type="submit" class="btn btn-primary">Add FAQ</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>




            </div>
        </div>
    @endsection
    @section('script')
        <script>
            $(document).ready(function() {
                $('#closeButton').on('click', function(e) {
                    $('#addBusForm')[0].reset();

                });

            });
        </script>
    @endsection
 
