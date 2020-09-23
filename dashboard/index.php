<?php
session_start();

$BASEDIR = '/home/vagrant/VM2/';

// Redirect to url
function Redirect(string $url)
{
  header('Location: ' . $url);
  die();
}

// Get existing hosts from vagrant_hosts.yml
function GetHosts(string $env = null)
{
  global $BASEDIR;
  global $CUSTOMERNAME;
  global $ENVIRONMENTS;

  if (empty($env)) {
    // If no environment was given, return all hosts for customers
    $files = array();
    // Push each environment's hosts to an array
    foreach ($ENVIRONMENTS as $env) {
      $file = yaml_parse_file("${BASEDIR}klanten/${CUSTOMERNAME}/${env}/vagrant_hosts.yml");
      array_push($files, $file);
    }
    return $files;
  } else {
    // Environment was given, return only those hosts
    // Set the hosts file path
    $file = yaml_parse_file("${BASEDIR}klanten/${CUSTOMERNAME}/${env}/vagrant_hosts.yml");
    // Parse the YAML file to a PHP array
    return array($file);
  }
}

function DemoCheck(string $env = null, string $name = null)
{
  if (empty($name)) {
    return $env == "test" ? 'disabled' : null;
  } else {
    return 0 < count(array_intersect(array_map('strtolower', explode('-', $name)), array('web01', 'web02', 'lb01', 'db01'))) && $env == 'test' ? 'disabled' : null;
  }
}

if (empty($_SESSION['customerName'])) {
  // User is not signed in, send to signin
  Redirect('/account/signin.php');
} else {
  // Set global variables
  $CUSTOMERNAME = $_SESSION['customerName'];
  $ENVIRONMENT = $_GET['env'];

  // Create array of environments
  $ENVIRONMENTS = array_diff(scandir("${BASEDIR}klanten/${CUSTOMERNAME}"), array('..', '.'));
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Very simple Self-Service Portal for school project for Virtualization Methods 2">
  <meta name="author" content="Thomas van den Nieuwenhoff">
  <title>Dashboard - VM2 Portaal</title>

  <!-- Bootstrap core CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

  <!-- Custom styles -->
  <link href="/styles/dashboard.css" rel="stylesheet">
</head>

<body>
  <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 mr-0 px-3" href="#"><?php echo $CUSTOMERNAME ?></a>
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-toggle="collapse" data-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <ul class="navbar-nav px-3">
      <li class="nav-item text-nowrap">
        <a class="nav-link" href="/processing/signout.php">Sign out</a>
      </li>
    </ul>
  </nav>

  <div class="container-fluid">
    <div class="row">
      <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
        <div class="sidebar-sticky pt-3">
          <ul class="nav flex-column">
            <li class="nav-item">
              <a class="nav-link" href="/account/index.php">
                <span data-feather="user"></span>
                Account
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="#">
                <span data-feather="cloud"></span>
                Dashboard <span class="sr-only">(current)</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/editor/index.php">
                <span data-feather="edit"></span>
                Editor
              </a>
            </li>
          </ul>
        </div>
      </nav>

      <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
          <h1 class="h2">Dashboard</h1>
          <div class="btn-toolbar mb-2 mb-md-0">
            <?php
            if (!empty($ENVIRONMENT)) {
              // When displaying a specific environment, display the option menu
              echo "<form action=\"/processing/machineAction.php\" method=\"post\">
                      <input type=\"hidden\" name=\"env\" value=\"${ENVIRONMENT}\">
                      <div class=\"btn-group mr-2\">
                        <input type=\"submit\" class=\"btn btn-sm btn-outline-success\" name=\"cmd\" value=\"Up\">
                        <input type=\"submit\" class=\"btn btn-sm btn-outline-secondary\" name=\"cmd\" value=\"Down\">
                        <input type=\"submit\" class=\"btn btn-sm btn-outline-danger\" name=\"cmd\" value=\"Delete\"" . DemoCheck($ENVIRONMENT) . ">
                        <input type=\"submit\" class=\"btn btn-sm btn-outline-info\" name=\"cmd\" value=\"Run Ansible\">
                        <a class=\"btn btn-sm btn-success\" href=\"/dashboard/addHost.php?env=${ENVIRONMENT}\" role=\"button\"><span data-feather=\"plus\"></span> Add Host</a>
                        <a class=\"btn btn-sm btn-danger " . DemoCheck($ENVIRONMENT) . "\" href=\"/processing/deleteEnv.php?env=${ENVIRONMENT}\" role=\"button\">
                          <span data-feather=\"minus\"></span> Delete Environment
                        </a>
                      </div>
                    </form>";
            }
            ?>
            <!-- Environment selector -->
            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" id="environmentDropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <span data-feather="layers"></span>
              <?php if (empty($_GET['env'])) {
                // When no environment is selected, display "Environments"
                echo "Environments";
              } else {
                // When a environment is selected, display its name
                echo $_GET['env'];
              }
              ?>
            </button>
            <!-- Environment dropdown -->
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="environmentDropdownMenuButton">
              <?php
              echo "<a class=\"dropdown-item\" href=\"?env=\">All</a>";
              echo "<div class=\"dropdown-divider\"></div>";
              foreach ($ENVIRONMENTS as $env) {
                // Loop through all environments for customer
                echo "<a class=\"dropdown-item\" href=\"?env=${env}\">${env}</a>";
              }
              if (empty($ENVIRONMENTS)) {
                // If there are no environments, display this message
                echo "<h6 class=\"dropdown-header\">No environments found</h6>";
              }
              ?>
            </div>
          </div>
        </div>
        <!-- Machine table -->
        <div class="table-responsive">
          <table class="table table-striped table-sm">
            <thead>
              <tr>
                <th>Environment</th>
                <th>Type</th>
                <th>Hostname</th>
                <th>OS</th>
                <th>IP</th>
                <th>RAM</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // Get all hosts for customer or environment
              $hosts_array = (empty($ENVIRONMENT)) ? GetHosts() : GetHosts($ENVIRONMENT);
              // Loop through each environment in array
              foreach ($hosts_array as $hosts) {
                if (!empty($hosts)) {
                  // Loop through each host in environment
                  foreach ($hosts as $host) {
                    // Display nicely formatted type
                    switch ($host['type']) {
                      case 'db':
                        $type = "Databaseserver";
                        break;
                      case 'lb':
                        $type = "Loadbalancer";
                        break;
                      case 'web':
                        $type = 'Webserver';
                        break;
                      default:
                        $type = "N/A";
                        break;
                    }
                    // Actual table row
                    echo "<tr>
                          <td>${host['env']}</td>
                          <td>${type}</td>
                          <td>${host['name']}</td>
                          <td>${host['os']}</td>
                          <td>${host['ip']}</td>
                          <td>${host['ram']}</td>
                          <td>
                            <form action=\"/processing/machineAction.php\" method=\"post\">
                              <input type=\"hidden\" name=\"vmName\" value=\"${host['name']}\">
                              <input type=\"hidden\" name=\"env\" value=\"${host['env']}\">
                              <input type=\"hidden\" name=\"type\" value=\"${host['type']}\">

                              <input type=\"submit\" class=\"btn btn-outline-success btn-sm\" title=\"Bring machine up\" name=\"cmd\" value=\"Up\">
                              <input type=\"submit\" class=\"btn btn-outline-secondary btn-sm\" title=\"Take machine down\" name=\"cmd\" value=\"Down\">
                              <input type=\"submit\" class=\"btn btn-outline-danger btn-sm\" title=\"Delete machine\" name=\"cmd\" value=\"Delete\"" . DemoCheck($host['env'], $host['name']) . ">
                              <input type=\"submit\" class=\"btn btn-outline-info btn-sm\" title=\"Run Ansible playbook\" name=\"cmd\" value=\"Run Ansible\">
                              <a class=\"btn btn-outline-dark btn-sm vm-edit-btn " . DemoCheck($host['env'], $host['name']) . "\" href=\"editHost.php?hostName=${host['name']}&env=${host['env']}\" title=\"Edit machine\" role=\"button\">
                                <span data-feather=\"edit-3\"></span>
                              </a>
                            </form>
                          </td>
                        </tr>";
                  }
                }
              }
              ?>
            </tbody>
          </table>
          <hr>
          <form class="form-inline" action="/processing/addEnv.php" method="post">
            <div class="form-group">
              <label class="sr-only" for="inputEnvName">Environment Name</label>
              <input type="text" class="form-control mb-2 mx-sm-2" id="inputEnvName" name="inputEnvName" placeholder="Environment Name">

              <button type="submit" class="btn btn-primary mb-2">Create</button>
            </div>
          </form>
        </div>
      </main>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.9.0/feather.min.js"></script>
  <script src="/scripts/dashboard.js"></script>
</body>

</html>