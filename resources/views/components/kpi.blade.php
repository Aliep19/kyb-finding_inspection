<div class="row">
<div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
    <div class="card">
        <div class="card-body p-3">
            <div class="row">
                <div class="col-8">
                    <div class="numbers">
                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Monthly Comparison</p>
   <h5 class="font-weight-bolder mb-0" id="comparison-card">
    <span class="text-muted">Loading...</span>
</h5>

<p class="text-xs text-secondary mb-0" id="this-month"></p>
<p class="text-xs text-secondary mb-0" id="prev-month"></p>
                    </div>
                </div>
                <div class="col-4 text-end">
                    <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                        <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
  <div class="card">
    <div class="card-body p-3">
      <div id="comparisonCarousel" class="carousel slide" data-bs-ride="carousel">

        <!-- Carousel inner -->
        <div class="carousel-inner">

          <!-- Slide 1: Painting -->
          <div class="carousel-item active">
            <div class="row">
              <div class="col-8">
                <div class="numbers">
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">Painting Comparison</p>
                  <h5 class="font-weight-bolder mb-0" id="painting-comparison-card">
                    <span class="text-muted">Loading...</span>
                  </h5>
                  <p class="text-xs text-secondary mb-0" id="painting-this-month"></p>
                  <p class="text-xs text-secondary mb-0" id="painting-prev-month"></p>
                </div>
              </div>
              <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                  <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                </div>
              </div>
            </div>
          </div>

          <!-- Slide 2: Not Painting -->
          <div class="carousel-item">
            <div class="row">
              <div class="col-8">
                <div class="numbers">
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">Not Painting Comparison</p>
                  <h5 class="font-weight-bolder mb-0" id="not-painting-comparison-card">
                    <span class="text-muted">Loading...</span>
                  </h5>
                  <p class="text-xs text-secondary mb-0" id="not-painting-this-month"></p>
                  <p class="text-xs text-secondary mb-0" id="not-painting-prev-month"></p>
                </div>
              </div>
              <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                  <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
<div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
  <div class="card">
    <div class="card-body p-3">
      <div id="ratioCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
        <div class="carousel-inner">

          <!-- Slide 1: Defect -->
          <div class="carousel-item active">
            <div class="row">
              <div class="col-8">
                <div class="numbers">
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">Defect Ratio Comparison</p>
                  <h5 class="font-weight-bolder mb-0" id="defect-comparison-card">
                    <span class="text-muted">Loading...</span>
                  </h5>
                  <p class="text-xs text-secondary mb-0" id="defect-this-month"></p>
                  <p class="text-xs text-secondary mb-0" id="defect-prev-month"></p>
                </div>
              </div>
              <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                  <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                </div>
              </div>
            </div>
          </div>

          <!-- Slide 2: Repair -->
          <div class="carousel-item">
            <div class="row">
              <div class="col-8">
                <div class="numbers">
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">Repair Ratio Comparison</p>
                  <h5 class="font-weight-bolder mb-0" id="repair-comparison-card">
                    <span class="text-muted">Loading...</span>
                  </h5>
                  <p class="text-xs text-secondary mb-0" id="repair-this-month"></p>
                  <p class="text-xs text-secondary mb-0" id="repair-prev-month"></p>
                </div>
              </div>
              <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                  <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                </div>
              </div>
            </div>
          </div>

          <!-- Slide 3: Reject -->
          <div class="carousel-item">
            <div class="row">
              <div class="col-8">
                <div class="numbers">
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">Reject Ratio Comparison</p>
                  <h5 class="font-weight-bolder mb-0" id="reject-comparison-card">
                    <span class="text-muted">Loading...</span>
                  </h5>
                  <p class="text-xs text-secondary mb-0" id="reject-this-month"></p>
                  <p class="text-xs text-secondary mb-0" id="reject-prev-month"></p>
                </div>
              </div>
              <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-danger shadow text-center border-radius-md">
                  <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
<!-- Card for Top 3 Workstations -->
<div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-body p-3">
            <div class="row">
                <div class="col-8">
                    <div class="numbers">
                        <!-- Judul -->
                        <p class="text-xs mb-1 text-uppercase fw-bold text-muted" style="letter-spacing: .5px;">
                            Top 3 Workstations (Defects)
                        </p>
                        <!-- Bulan -->
                        <h5 class="fw-bolder mb-2 text-dark" id="top-workstations-month" style="font-size: 1rem;">
                            <!-- Dynamic dari JS -->
                        </h5>
                        <!-- List Workstations -->
                        <ul class="list-unstyled mb-0 text-secondary" id="top-workstations-list" style="font-size: .9rem; line-height: 1.6;">
                            <li>Loading...</li>
                        </ul>
                    </div>
                </div>
                <div class="col-4 text-end">
                    <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                        <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



</div>
