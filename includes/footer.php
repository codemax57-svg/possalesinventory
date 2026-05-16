<?php
/**
 * Footer Component
 */
?>
    </div> <!-- content-area -->
    
    <!-- Footer -->
    <footer class="footer">
        <div class="row align-items-center">
            <div class="col-md-4 text-start">
                <p>&copy; <?php echo APP_YEAR; ?> <?php echo APP_NAME; ?>. All rights reserved.</p>
            </div>
            <div class="col-md-4 text-center">
                <p><strong><?php echo BUSINESS_NAME; ?></strong><br>
                   <?php echo BUSINESS_ADDRESS; ?><br>
                   <?php echo BUSINESS_PHONE; ?></p>
            </div>
            <div class="col-md-4 text-end">
                <p>Version <?php echo APP_VERSION; ?><br>
                   Last Updated: <span id="current-time"></span></p>
            </div>
        </div>
    </footer>
    </div> <!-- main-content -->
</div> <!-- page-wrapper -->

<!-- Bootstrap JS -->
<script src="<?php echo ASSET_URL; ?>/js/bootstrap.bundle.min.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Moment JS -->
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>

<!-- Date Range Picker -->
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<!-- Sweet Alert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.31/dist/sweetalert2.all.min.js"></script>

<!-- Custom Scripts -->
<script src="<?php echo ASSET_URL; ?>/js/script.js"></script>

<script>
// Update current time in footer
function updateTime() {
    const now = new Date();
    document.getElementById('current-time').textContent = now.toLocaleString();
}

updateTime();
setInterval(updateTime, 1000);

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
</script>

<?php if (isset($additional_js)): ?>
    <?php echo $additional_js; ?>
<?php endif; ?>
</body>
</html>