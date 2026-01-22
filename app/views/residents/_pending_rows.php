<?php
/**
 * Partial view for rendering rows in the 'Pending Review' table.
 * This is included by app/views/residents/index.php when $isPendingView is true.
 */
?>
<?php foreach ($residents as $r): ?>
    <tr data-id="<?= $r['id'] ?>">
        <td><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></td>
        <td><?= htmlspecialchars($r['age']) ?></td>
        <td><?= htmlspecialchars($r['gender']) ?></td>
        <td><?= htmlspecialchars($r['house_no'] . ' ' . $r['street'] . ', Purok ' . $r['purok']) ?></td>
        <td><?= date('M d, Y h:i A', strtotime($r['created_at'])) ?></td>
        <td>
            <div class="actions-column" style="display: flex; gap: 5px;">
                <form action="<?= $base_url ?>/residents/approve" method="POST" style="margin:0;">
                    <?= Csrf::getField() ?>
                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                    <button type="submit" class="action-btn btn-approve" title="Approve" style="border:none; cursor:pointer;">
                        <span class="material-icons">check</span>
                    </button>
                </form>

                <form action="<?= $base_url ?>/residents/reject" method="POST" style="margin:0;" onsubmit="return confirm('Are you sure you want to reject this entry?');">
                    <?= Csrf::getField() ?>
                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                    <button type="submit" class="action-btn btn-reject" title="Decline" style="border:none; cursor:pointer;">
                        <span class="material-icons">close</span>
                    </button>
                </form>

                <button class="action-btn btn-view moreBtn" data-id="<?= $r['id'] ?>" title="View Details">
                    <span class="material-icons">visibility</span>
                </button>
            </div>
        </td>
    </tr>
<?php endforeach; ?>