@extends('layouts.user-dashboard-app')

@section('title', 'Search')

@push('styles')
     <style>
    body {
      background: #f4f6f9;
    }
    .search-box {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      padding: 30px;
      margin: 30px auto;
    }
    h4 {
      font-weight: bold;
      color: #c62828;
      border-left: 5px solid #c62828;
      padding-left: 10px;
      margin-bottom: 25px;
    }
    label {
      font-weight: 600;
      font-size: 15px;
      color: #333;
    }
    .form-select, .form-check-input {
      border-radius: 8px;
    }
    .btn-custom {
      background: linear-gradient(45deg, #e53935, #d32f2f);
      border: none;
      color: #fff;
      font-weight: 600;
      border-radius: 8px;
      padding: 10px 30px;
    }
    .btn-custom:hover {
      background: linear-gradient(45deg, #d32f2f, #b71c1c);
    }
  </style>
@endpush

@section('sidebar')
.
@endsection

@section('subnavbar')
.
@endsection

@section('content')

<div class="container">
  <div class="row justify-content-center">
    <div class="col-12 col-md-10">
      <div class="search-box">
        <h4>Find Bangladeshi Profiles Here</h4>

        <form>
          <!-- Age -->
          <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Age</label>
            <div class="col-sm-9 d-flex">
              <select class="form-select me-2">
                <option>20</option><option>21</option><option>22</option>
              </select>
              <span class="align-self-center me-2">to</span>
              <select class="form-select">
                <option>27</option><option>28</option><option>29</option>
              </select>
              <span class="align-self-center ms-2">Years</span>
            </div>
          </div>

          <!-- Height -->
          <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Height</label>
            <div class="col-sm-9 d-flex">
              <select class="form-select me-2">
                <option>132 cm</option><option>140 cm</option><option>150 cm</option>
              </select>
              <span class="align-self-center me-2">to</span>
              <select class="form-select">
                <option>162 cm</option><option>170 cm</option><option>180 cm</option>
              </select>
            </div>
          </div>

          <!-- Marital Status -->
          <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Marital Status</label>
            <div class="col-sm-9">
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox"> <label class="form-check-label">Any</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox"> <label class="form-check-label">Unmarried</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox"> <label class="form-check-label">Widow/Widower</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox"> <label class="form-check-label">Divorced</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox"> <label class="form-check-label">Separated</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox"> <label class="form-check-label">Married</label>
              </div>
            </div>
          </div>

          <!-- Religion -->
          <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Religion</label>
            <div class="col-sm-9">
              <select class="form-select">
                <option>Islam</option>
                <option>Hindu</option>
                <option>Christian</option>
              </select>
            </div>
          </div>

          <!-- Sect -->
          <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Sect</label>
            <div class="col-sm-9 d-flex">
              <select multiple class="form-select me-2" style="height:120px;">
                <option>Any</option>
                <option>Hanafi</option>
                <option>Shia</option>
                <option>Sunni</option>
              </select>
              <select multiple class="form-select" style="height:120px;"></select>
            </div>
          </div>

          <!-- Mother Tongue -->
          <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Mother Tongue</label>
            <div class="col-sm-9 d-flex">
              <select multiple class="form-select me-2" style="height:120px;">
                <option>Bengali</option>
                <option>Hindi</option>
                <option>English</option>
              </select>
              <select multiple class="form-select" style="height:120px;"></select>
            </div>
          </div>

          <!-- Country -->
          <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Country living in</label>
            <div class="col-sm-9 d-flex">
              <select multiple class="form-select me-2" style="height:120px;">
                <option>Bangladesh</option>
                <option>India</option>
                <option>USA</option>
              </select>
              <select multiple class="form-select" style="height:120px;"></select>
            </div>
          </div>

          <!-- Education -->
          <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Education</label>
            <div class="col-sm-9 d-flex">
              <select multiple class="form-select me-2" style="height:120px;">
                <option>Any</option>
                <option>Bachelors</option>
                <option>Masters</option>
                <option>PhD</option>
              </select>
              <select multiple class="form-select" style="height:120px;"></select>
            </div>
          </div>

          <!-- Options -->
          <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Options</label>
            <div class="col-sm-9">
              <div class="form-check">
                <input class="form-check-input" type="checkbox"> <label class="form-check-label">Show profiles with photo</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" checked> <label class="form-check-label">Profiles already contacted</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" checked> <label class="form-check-label">Profiles already viewed</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" checked> <label class="form-check-label">Shortlisted profiles</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" checked> <label class="form-check-label">Profiles I have blocked</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" checked> <label class="form-check-label">Profiles I have ignored</label>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="text-center">
            <button class="btn btn-custom">🔍 Search</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


@endsection