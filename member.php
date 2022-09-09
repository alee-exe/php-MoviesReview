<!DOCTYPE HTML>
<html lang="en">
<!-- Made by Andy Lee-->
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="movie.ico" type="image">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha256-pasqAKBDmFT4eHoN2ndd6lN370kFiGUFyTiUHWhU7k8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            // Function to edit the watchlist name and desc when edit button is clicked
            $('#buy').click(function() {
                window.location = 'membership.php';
            });
            $('#try').click(function() {
                window.location = 'trial.php';
            });
        });
    </script>
    <title>Membership</title>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="span6" style="float:none; margin:auto;">
                <h2>Become a Member today!</h2>
                <hr>
                </hr>
                <button type="button" class="btn btn-primary btn-lg btn-warning" data-toggle="modal" data-target="#membershipModal">Buy a 1 Year Membership £18.99</button><br></br>
                <h4 class="mt-1"> Or... </h4>
                <button type="button" class="btn btn-primary btn-lg btn-info mt-4" data-toggle="modal" data-target="#trialModal">Try a 1 Month Free Trial</button><br></br>
            </div>
        </div>

        <!-- Membership Modal -->
        <div class="modal fade" id="membershipModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Buy a 1 Year Membership?</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>Fortunately, this website does not have a payment system so Membership is technically free!</p>
                        <p class="text-secondary"><small>Once you press the Buy button, a Full Membership will be instantly added to your account.</small></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="buy">Buy Membership</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trial Modal -->
        <div class="modal fade" id="trialModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Try a Free Trial?</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>By continuing, a Free Trial will be added to your account with no payment charged.</p>
                        <p class="text-secondary"><small>Once you press the Try button, a Trial Membership will be instantly added to your account.</small></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="try">Try Membership</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>