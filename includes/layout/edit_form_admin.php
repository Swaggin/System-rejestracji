<?php
  require_once(dirname(__DIR__) . '/components/calendar.php');
  require_once(dirname(__DIR__) . '/helpers/db.php');

  $sql = 'SELECT * FROM users';
  $stmt = $db_conn->prepare($sql);
  $stmt->execute();
  $users = $stmt->fetchAll();
?>

<div class="dashboard">
  <div class="dashboard__sidebar">
    <?php include_once(dirname(__DIR__) . '/layout/sidebar.php'); ?>
  </div>
  <div class="dashboard__content">
    <div class="info">
      <table>
        <tr>
          <th>ID</th>
          <th>Imię</th>
          <th>Nazwisko</th>
          <th>Płeć</th>
        </tr>
        <?php foreach ($users as $user): ?>
          <tr>
            <td>
              <form method="get">
                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                <button name="lista" type="submit" class="button">Edytuj</button>
              </form>
            </td>
            <td><?php echo $user['username']; ?></td>
            <td><?php echo $user['firstname']; ?></td>
            <td><?php echo $user['lastname']; ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
      <?php if (isset($_GET['user_id'])): ?>
        <?php
        $sql = 'SELECT * FROM users WHERE id=:user_id';
        $stmt = $db_conn->prepare($sql);
        $stmt->execute(array(':user_id' => $_GET['user_id']));
        $user = $stmt->fetch();
        ?>
        <h2><br><br>Szczegóły użytkownika</h2>
        <table class="vertical">
          <tr>
            <th>ID</th>
            <td><?php echo $user['id']; ?></td>
          </tr>
          <tr>
            <th>Login</th>
            <td><?php echo $user['username']; ?></td>
          </tr>
          <tr>
            <th>Imię</th>
            <td><?php echo $user['firstname']; ?></td>
          </tr>
          <tr>
            <th>Nazwisko</th>
            <td><?php echo $user['lastname']; ?></td>
          </tr>
          <tr>
            <th>Adres email:</th>
            <td><?php echo $user['email']; ?></td>
          </tr>
          <tr>
            <th>Płeć</th>
            <td>
              <?php
                $gender = '';

                if (isset($user['gender']) && $user['gender'] == 'm') $gender = 'Mężczyzna';
                if (isset($user['gender']) && $user['gender'] == 'f') $gender = 'Kobieta';
                if (isset($user['gender']) && $user['gender'] == 'o') $gender = 'Inna';

                echo $gender;
              ?>
            </td>
          </tr>
          <form action="update_user.php" method="POST">
            <tr>
              <th>Admin?</th>
              <?php if($user['id'] === "e708860e6cb8c") { ?>
                <td>
                  <input type="radio" name="is_admin" value="1" <?php if ($user['is_admin']) echo 'checked'; ?> disabled> Tak
                  <input type="radio" name="is_admin" value="0" <?php if (!$user['is_admin']) echo 'checked'; ?> disabled> Nie
                </td> <?php }
              else
              { ?>
                <td>
                  <input type="radio" name="is_admin" value="1" <?php if ($user['is_admin']) echo 'checked'; ?> > Tak
                  <input type="radio" name="is_admin" value="0" <?php if (!$user['is_admin']) echo 'checked'; ?> > Nie
                </td> <?php } ?>
            </tr>
            <tr>
              <th>Doktor?</th>
              <td>
                <input type="radio" name="is_doctor" value="1" <?php if ($user['is_doctor']) echo 'checked'; ?>> Tak
                <input type="radio" name="is_doctor" value="0" <?php if (!$user['is_doctor']) echo 'checked'; ?>> Nie
              </td>
            </tr>
            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
            <tr>
              <td>
                <?php echo 'Wprowadzone zmiany będą obowiązywać od nowej sesji logowania.'; ?>
              </td>
              <td>
                <input type="submit" value="Zapisz zmiany"><br><br>
              </td>
            </tr>
          </form>
          <tr>
            <th>Usuń użytkownika:</th>
            <td>
              <form method="post">
                <input type="hidden" name="user_id1" value="<?php echo $user['id']; ?>">
                <button type="submit">Usuń</button>
              </form>
              <?php
                if (isset($_POST["user_id1"])) {
                  $user_id = $_POST["user_id1"];
                  $sql = 'SELECT id FROM users WHERE username=:username';
                  $stmt = $db_conn->prepare($sql);
                  $stmt->execute(array(':username' => $_SESSION['username']));
                  $user = $stmt->fetch();
                  $session_id = $user['id'];
                  if ($user_id === $session_id) {
                    echo '<div class="alert alert-success" id="delete-message">Nie możesz usunąć swojego konta.</div>';
                  }
                  else if ($user_id === "e708860e6cb8c"){
                    echo '<div class="alert alert-success" id="delete-message">Nie możesz usunąć konta ROOTa.</div>';
                  }
                  else {
                    $sql = 'DELETE FROM users WHERE id=:user_id';
                    $stmt = $db_conn->prepare($sql);
                    $stmt->execute(array(':user_id' => $user_id));
                    echo '<div class="alert alert-success" id="delete-message">Użytkownik został usunięty z bazy danych. Odśwież, by zobaczyć zmiany.</div>';
                  }
                }
              ?>
            </td>
          </tr>
        </table>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
  var refresh = true;
  setTimeout(function() {
    document.getElementById('delete-message').style.display = 'none';
  }, 5000);
</script>
