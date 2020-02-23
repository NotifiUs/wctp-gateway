<div class="modal fade" data-backdrop="static" id="tlsWarningModal" tabindex="-1" role="dialog" aria-labelledby="tlsWarningModal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow-sm">
            <div class="modal-header border-bottom-0">

                <h5 class="modal-title" id="tlsWarningModal">Insecure TLS connections enabled</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body my-0 py-0">
                <div class="container-fluid px-0 mx-0 my-0">
                    <p>
                        You have enabled outbound TLS connections to Enterprise Hosts that support <a href="https://www.cloudflare.com/learning/ssl/why-use-tls-1.3/">broken versions of TLS</a>, or un-verified certificates (such as self-signed.)
                        This warning message will not dismiss until you disable insecure TLS support.
                    </p>
                    <div class="alert shadow-sm alert-warning border-warning font-weight-bold">
                        <i class="fas fa-exclamation-triangle"></i> Configuration not recommended or considered compliant for most regulations (HIPAA, PCI, SOX, etc.)
                    </div>
                      <p>
                          These options can only be enabled or disabled by accessing this servers environment configuration file from the console of the server (or remote access such as <a href="https://www.digitalocean.com/community/tutorials/how-to-configure-ssh-key-based-authentication-on-a-linux-server">SSH with public keys</a>) and adding or editing the value below.
                      </p>
                          <blockquote>
                              By default <b>your Enterprise Host (your IS web server)</b> must support at least TLS version(s) 1.2+ with a <a href="https://letsencrypt.org/">valid SSL/TLS certificate</a>
                          </blockquote>

                        <div class="card">
                            <h5 class="card-header bg-light font-bold"><i class="text-muted">{{ base_path('.env') }}</i></h5>
                        <div class="card-body py-4 my-0">
                            <code>
                            <span class="text-dark"># Sets Guzzle HTTP library to ignore certificate errors<br>
                                # and support older TLS versions when connecting to Enterprise Host urls.<br>
                            </span>
                                TLS_VERIFY_CERTIFICATES=false<br>
                                TLS_PROTOCOL_SUPPORT=CURL_SSLVERSION_TLSv1_0<br>
                                <br>
                                <span class="text-dark"># Sets Guzzle HTTP library to verify certificates<br>
                            # and use the latest TLS version when connecting to Enterprise Host urls.<br>
                                </span>
                                TLS_VERIFY_CERTIFICATES=true<br>
                                TLS_PROTOCOL_SUPPORT=CURL_SSLVERSION_TLSv1_3<br>
                                <br>
                            <span class="text-dark"># This is the default configuration<br>
                            # Defaults: TLS_VERIFY_CERTIFICATES=true<br>
                            # Defaults: TLS_PROTOCOL_SUPPORT=CURL_SSLVERSION_TLSv1_2<br>
                            # (omitted)<br>
                            </span>

                            </code>
                        </div>
                    </div>
                    <div class="card-body py-2- my-0 text-muted">
                        <h5>Inbound TLS Configuration</h5>
                        To disable or enable inbound support for specific TLS versions or cyphers, you must configure this server's web application server.
                        This setting cannot be controlled directly from this application &mdash; please review your web server's documentation.
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
