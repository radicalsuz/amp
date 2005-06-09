                </fieldset>
            </div>

        </td>
    </tr>
    </table> 

    <p id="footer">
        AMP <?= AMP_SYSTEM_VERSION_ID ?> for <?= $SiteName ?><br/>
        Please report problems to <a href="mailto:<?= $admEmail ?>"><?= $admEmail ?></a>
    </p>

  </body>
</html>

<?php 
ob_end_flush();
?>
