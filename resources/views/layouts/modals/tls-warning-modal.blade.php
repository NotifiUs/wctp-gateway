<div class="modal fade" data-backdrop="static" id="tlsWarningModal" tabindex="-1" role="dialog"
     aria-labelledby="tlsWarningModal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-fullscreen-lg-down" role="document">
        <div class="modal-content shadow-sm">
            <div class="modal-header border-bottom-0">

                <h5 class="modal-title" id="tlsWarningModal">Insecure TLS connections enabled</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                </button>
            </div>
            <div class="modal-body my-0 py-0">
                <div class="container-fluid px-0 mx-0 my-0">
                    <p>
                        You have enabled outbound TLS connections to Enterprise Hosts that support <a
                            href="https://www.cloudflare.com/learning/ssl/why-use-tls-1.3/">broken versions of TLS</a>,
                        or un-verified certificates (such as self-signed.)
                        This warning message will not dismiss until you disable insecure TLS support.
                    </p>
                    <div class="alert shadow-sm alert-warning border-warning fw-bold">
                        <i class="fas fa-exclamation-triangle"></i> Configuration not recommended or considered
                        compliant for most regulations (HIPAA, PCI, SOX, etc.)
                    </div>
                    <p>
                        Enabled or disable by accessing this server's environment configuration file from the console of
                        the server (or remote access such as <a
                            href="https://www.digitalocean.com/community/tutorials/how-to-configure-ssh-key-based-authentication-on-a-linux-server">SSH
                            with public keys</a>) and adding or editing the value below.
                    </p>
                    <blockquote>
                        By default <b>your Enterprise Host (your Intelligent Series web server)</b> must support at
                        least TLS version(s) 1.2+ with a <a href="https://letsencrypt.org/">valid SSL/TLS
                            certificate</a>
                    </blockquote>

                    <div class="card">
                        <h5 class="card-header bg-light font-bold"><i class="text-muted">{{ base_path('.env') }}</i>
                        </h5>
                        <div class="card-body py-4 my-0">
                            <code>
                            <span class="text-dark"># Sets Guzzle HTTP library to ignore certificate errors<br>
                                # and support older TLS versions when connecting to Enterprise Host urls.<br>
                            </span>
                                TLS_VERIFY_CERTIFICATES=false<br>
                                TLS_PROTOCOL_SUPPORT=CURL_SSLVERSION_TLSv1_0<br>
                                TLS_CIPHER_LIST="DEFAULT:@SECLEVEL=1" #This is less secure than DEFAULT, for DH key exchange issues
                                <br>
                                <span class="text-dark"># Sets Guzzle HTTP library to verify certificates<br>
                            # and use the latest TLS version when connecting to Enterprise Host urls.<br>
                                </span>
                                TLS_VERIFY_CERTIFICATES=true<br>
                                TLS_PROTOCOL_SUPPORT=CURL_SSLVERSION_TLSv1_3<br>
                                TLS_CIPHER_LIST="DEFAULT"<br>
                                <br>
                                <span class="text-dark"># Default configuration<br>
                            # Defaults: TLS_VERIFY_CERTIFICATES=true<br>
                            # Defaults: TLS_PROTOCOL_SUPPORT=CURL_SSLVERSION_TLSv1_2<br>
                            # (omitted)<br>
                            </span>
                                <span class="text-dark"># Sets Guzzle HTTP library to verify certificates<br>
                            # and use the latest TLS version when connecting to Enterprise Host urls.<br>
                                </span>
                                TLS_VERIFY_CERTIFICATES=true<br>
                                TLS_PROTOCOL_SUPPORT=CURL_SSLVERSION_TLSv1_3<br>
                                <br>
                                <span class="text-dark"># Sunwire Certificates<br>
                            #  Configuration applies to Sunwire only<br>
                            #GUZZLE_ALLOW_SELFSIGNED=true<br>
                            # (omitted)<br>
                            </span>

                            </code>
                        </div>
                    </div>
                    <div class="card-body py-2- my-0 text-muted">
                        <h5>Inbound TLS Configuration</h5>
                        To disable or enable inbound support for specific TLS versions/cyphers, you must configure this
                        server's web application server.
                        Please review your web server's documentation.
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
