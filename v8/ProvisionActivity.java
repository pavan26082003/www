// Copyright 2025 Factor Enovations Pvt Ltd
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//     http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.

package tech.efactor.ui.activities;


import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.os.Handler;
import android.util.Log;
import android.view.View;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;
import androidx.cardview.widget.CardView;
import androidx.core.widget.ContentLoadingProgressBar;

import com.google.android.material.appbar.MaterialToolbar;
import com.google.gson.JsonObject;

import org.greenrobot.eventbus.EventBus;
import org.greenrobot.eventbus.Subscribe;
import org.greenrobot.eventbus.ThreadMode;

import java.util.TimeZone;

import tech.efactor.AppConstants;
import tech.efactor.cloudapi.ApiManager;
import tech.efactor.proto.DeviceConnectionEvent;
import tech.efactor.proto.EfactorConstants;
import tech.efactor.proto.EfactorDeviceOperationRequest;
import tech.efactor.proto.EfactorProvisionManager;
import tech.efactor.proto.listeners.ApiLibResponseListener;
import tech.efactor.proto.listeners.ProvisionListener;
import tech.efactor.proto.listeners.ResponseListener;
import tech.efactor.smartthings.R;
import tech.efactor.ui.models.UpdateEvent;


public class ProvisionActivity extends AppCompatActivity {

    private static final String TAG = ProvisionActivity.class.getSimpleName();

    private static final long ADD_DEVICE_REQ_TIME = 15000;
    private static final long NODE_STATUS_REQ_TIME = 35000;

    private ImageView tick5, tick6, tick7, tick8, tick9,tick3,tick4,tick2,tick1;
    private ContentLoadingProgressBar progress5, progress6, progress7, progress8, progress9,progress3,progress4,progress2,progress1;
    private TextView tvErrAtStep5, tvErrAtStep6, tvErrAtStep7, tvErrAtStep8,tvErrAtStep9,tvErrAtStep2, tvErrAtStep1;
    private TextView tvProvSuccess, tvProvError;

    private CardView btnOk;
    private TextView txtOkBtn;

    private int addDeviceReqCount = 0;
    private String ssidValue, passphraseValue = "";
    private String receivedNodeId, secretKey;

    private ApiManager apiManager;
    private Handler handler;
    private EfactorProvisionManager provisionManager;
    private boolean isProvisioningCompleted = false;


    @Override
    protected void onCreate(Bundle savedInstanceState) {

        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_provision);

        Intent intent = getIntent();
        ssidValue = intent.getStringExtra(AppConstants.KEY_SSID);
        passphraseValue = intent.getStringExtra(AppConstants.KEY_PASSWORD);
        provisionManager = EfactorProvisionManager.getInstance(getApplicationContext());

        handler = new Handler();
        apiManager = ApiManager.getInstance(getApplicationContext());
        initViews();

        Log.d(TAG, "Selected AP - " + ssidValue);
        EventBus.getDefault().register(this);
        showLoading();
        doStep1();
    }

    @Override
    public void onBackPressed() {
        provisionManager.getEfactorDevice().disconnectDevice();
        super.onBackPressed();
    }

    @Override
    protected void onDestroy() {

        apiManager.cancelRequestStatusPollingTask();
        handler.removeCallbacks(getNodeStatusTask);
        handler.removeCallbacks(nodeStatusReqFailed);
        EventBus.getDefault().unregister(this);
        super.onDestroy();
    }

    @Subscribe(threadMode = ThreadMode.MAIN)
    public void onEvent(UpdateEvent event) {
        Log.d(TAG, "ON UPDATE EVENT RECEIVED : " + event.getEventType());

        switch (event.getEventType()) {

            case EVENT_DEVICE_ADDED:
                tick8.setImageResource(R.drawable.ic_checkbox_on);
                tick8.setVisibility(View.VISIBLE);
                progress8.setVisibility(View.GONE);
                doStep9();
                break;

            case EVENT_ADD_DEVICE_TIME_OUT:
                tick8.setImageResource(R.drawable.ic_alert);
                tick8.setVisibility(View.VISIBLE);
                progress8.setVisibility(View.GONE);
                doStep9();
                break;
        }
    }

    @Subscribe(threadMode = ThreadMode.MAIN)
    public void onEvent(DeviceConnectionEvent event) {

        Log.d(TAG, "On Device Connection Event RECEIVED : " + event.getEventType());

        switch (event.getEventType()) {

            case EfactorConstants.EVENT_DEVICE_DISCONNECTED:
                if (!isFinishing() && !isProvisioningCompleted) {
                    showAlertForDeviceDisconnected();
                }
                break;
            case EfactorConstants.EVENT_DEVICE_SESSION_ESTABLISHED:
                Log.d(TAG, "On Device Connection Event RECEIVED : " + event.getEventType());
                sessionInitSuccess();
                break;
            case EfactorConstants.EVENT_APPLY_WIFI_CONFIG:
                Log.d(TAG, "On Device Connection Event RECEIVED : " + event.getEventType());
                applyWifiConfig();
                break;
            case EfactorConstants.EVENT_APPLY_WIFI_CONFIG_FAILED:
                Log.d(TAG, "WiFi Config Applied");
                progress5.setVisibility(View.GONE);
                tick5.setImageResource(R.drawable.ic_alert);
                tick5.setVisibility(View.VISIBLE);
                runOnUiThread(new Runnable() {
                    @Override
                    public void run() {

                        doStep7(false);
                    }
                });
                break;

            case EfactorConstants.EVENT_DEVICE_ADDED:
                tick8.setImageResource(R.drawable.ic_checkbox_on);
                tick8.setVisibility(View.VISIBLE);
                progress8.setVisibility(View.GONE);
                doStep9();
                break;

            case EfactorConstants.EVENT_ADD_DEVICE_TIME_OUT:
                tick8.setImageResource(R.drawable.ic_alert);
                tick8.setVisibility(View.VISIBLE);
                progress8.setVisibility(View.GONE);
                doStep9();

                break;
        }
    }


    private View.OnClickListener okBtnClickListener = new View.OnClickListener() {

        @Override
        public void onClick(View v) {
            provisionManager.getEfactorDevice().disconnectDevice();
            finish();
        }
    };

    private void initViews() {

        MaterialToolbar toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
        getSupportActionBar().setDisplayHomeAsUpEnabled(false);
        getSupportActionBar().setDisplayShowHomeEnabled(false);
        getSupportActionBar().setTitle(R.string.title_activity_provisioning);

        tick5 = findViewById(R.id.iv_tick_5);
        tick6 = findViewById(R.id.iv_tick_6);
        tick7 = findViewById(R.id.iv_tick_7);
        tick8 = findViewById(R.id.iv_tick_8);
        tick9 = findViewById(R.id.iv_tick_9);
        tick3 = findViewById(R.id.iv_tick_3);
        tick4 = findViewById(R.id.iv_tick_4);
        tick2 = findViewById(R.id.iv_tick_2);
        tick1 = findViewById(R.id.iv_tick_1);

        progress5 = findViewById(R.id.prov_progress_5);
        progress6 = findViewById(R.id.prov_progress_6);
        progress7= findViewById(R.id.prov_progress_7);
        progress8 = findViewById(R.id.prov_progress_8);
        progress9 = findViewById(R.id.prov_progress_9);
        progress3 = findViewById(R.id.prov_progress_3);
        progress4 = findViewById(R.id.prov_progress_4);
        progress2 = findViewById(R.id.prov_progress_2);
        progress1 = findViewById(R.id.prov_progress_1);


        tvErrAtStep5 = findViewById(R.id.tv_prov_error_5);
        tvErrAtStep6 = findViewById(R.id.tv_prov_error_6);
        tvErrAtStep7 = findViewById(R.id.tv_prov_error_7);
        tvErrAtStep8 = findViewById(R.id.tv_prov_error_8);
        tvErrAtStep9 = findViewById(R.id.tv_prov_error_9);
        tvErrAtStep2=findViewById(R.id.tv_prov_error_2);
        tvErrAtStep1 = findViewById(R.id.tv_prov_error_1);



        tvProvSuccess = findViewById(R.id.tv_prov_success);
        tvProvError = findViewById(R.id.tv_prov_error);

        btnOk = findViewById(R.id.btn_ok);
        txtOkBtn = findViewById(R.id.text_btn);
        btnOk.findViewById(R.id.iv_arrow).setVisibility(View.GONE);

        txtOkBtn.setText(R.string.btn_done);
        btnOk.setOnClickListener(okBtnClickListener);
    }

    private void doStep4() {
        tick3.setImageResource(R.drawable.ic_checkbox_on);
        //tick1.setVisibility(View.GONE);
        progress3.setVisibility(View.GONE);
        progress4.setVisibility(View.VISIBLE);

    }
    private void doStep5() {

        progress4.setVisibility(View.GONE);
        tick4.setImageResource(R.drawable.ic_checkbox_on);
        tick5.setVisibility(View.GONE);
        progress5.setVisibility(View.VISIBLE);

    }
    private void doStep3() {
        tick2.setImageResource(R.drawable.ic_checkbox_on);
        tick2.setVisibility(View.VISIBLE);
        progress2.setVisibility(View.GONE);

    }
    private void doStep2() {

        tick1.setImageResource(R.drawable.ic_checkbox_on);
        tick1.setVisibility(View.VISIBLE);
        progress1.setVisibility(View.GONE);
        tick2.setVisibility(View.GONE);
        progress2.setVisibility(View.VISIBLE);
    }
    private void doStep1() {
        tick1.setVisibility(View.GONE);
        progress1.setVisibility(View.VISIBLE);
        associateDevice();
    }
    private void doStep6() {

        tick5.setImageResource(R.drawable.ic_checkbox_on);
        tick5.setVisibility(View.VISIBLE);
        progress5.setVisibility(View.GONE);
        tick6.setVisibility(View.GONE);
        progress6.setVisibility(View.VISIBLE);
    }

    private void doStep7(boolean isSuccessInStep2) {

        if (isSuccessInStep2) {
            tick6.setImageResource(R.drawable.ic_checkbox_on);
        } else {
            tick6.setImageResource(R.drawable.ic_alert);
        }

        tick6.setVisibility(View.VISIBLE);
        progress6.setVisibility(View.GONE);
        tick7.setVisibility(View.GONE);
        progress7.setVisibility(View.VISIBLE);

        handler.postDelayed(addDeviceTask, ADD_DEVICE_REQ_TIME);
    }

    private void doStep8() {


        tick7.setImageResource(R.drawable.ic_checkbox_on);
        tick7.setVisibility(View.VISIBLE);
        progress7.setVisibility(View.GONE);
        tick8.setVisibility(View.GONE);
        progress8.setVisibility(View.VISIBLE);
    }

    private void doStep9() {

        Log.d(TAG, "================= Do step 9 =================");
        Log.d(TAG, "Received node id : " + receivedNodeId);


        tick9.setVisibility(View.GONE);
        progress9.setVisibility(View.VISIBLE);
        handler.postDelayed(nodeStatusReqFailed, NODE_STATUS_REQ_TIME);

        apiManager.FETCHNODE(receivedNodeId, new ApiLibResponseListener() {

            @Override
            public void onSuccess(Bundle data) {
                Log.e(TAG, "Get node details - success");
                handler.postDelayed(getNodeStatusTask, 1000);
            }

            @Override
            public void onResponseFailure(Bundle exception) {
                Log.e(TAG, "Get node details - failure");
                handler.postDelayed(getNodeStatusTask, 1000);
            }

            @Override
            public void onNetworkFailure(Bundle exception) {
                Log.e(TAG, "Get node details - failure");
                handler.postDelayed(getNodeStatusTask, 1000);
            }
        });
    }

    private void sessionInitSuccess() {
        // Nothing to do here
        Log.d(TAG, "sessionInitSuccess");
        runOnUiThread(new Runnable() {
            @Override
            public void run() {
                doStep2();
            }
        });

    }

    private void applyWifiConfig() {
        // Nothing to do here
        Log.d(TAG, "applyWifiConfig");
        runOnUiThread(new Runnable() {
            @Override
            public void run() {
                doStep5();
            }
        });

    }
    private void provision() {

        Log.d(TAG, "+++++++++++++++++++++++++++++ IOE DEVICE PROVISION +++++++++++++++++++++++++++++");

        provisionManager.getEfactorDevice().provision(ssidValue, passphraseValue, new ProvisionListener() {

            @Override
            public void createSessionFailed(Exception e) {
                runOnUiThread(new Runnable() {
                    @Override
                    public void run() {
                        Toast.makeText(ProvisionActivity.this, R.string.error_session, Toast.LENGTH_LONG).show();
                    }
                });
            }

            @Override
            public void wifiConfigSent() {
                // Nothing to do here
                Log.d(TAG, "WiFi Config sent");
                runOnUiThread(new Runnable() {
                    @Override
                    public void run() {
                        doStep4();
                    }
                });

            }


            @Override
            public void wifiConfigFailed(Exception e) {

                runOnUiThread(new Runnable() {

                    @Override
                    public void run() {

                        tick5.setImageResource(R.drawable.ic_error);
                        tick5.setVisibility(View.VISIBLE);
                        progress5.setVisibility(View.GONE);
                        tvErrAtStep6.setVisibility(View.VISIBLE);
                        tvErrAtStep6.setText(R.string.error_prov_step_2);
                        hideLoading();
                    }
                });

            }

            @Override
            public void wifiConfigApplied() {

                Log.d(TAG, "WiFi Config Applied");
                runOnUiThread(new Runnable() {
                    @Override
                    public void run() {

                        doStep6();
                    }
                });
            }

            @Override
            public void wifiConfigApplyFailed(Exception e) {

                runOnUiThread(new Runnable() {

                    @Override
                    public void run() {

                        tick5.setImageResource(R.drawable.ic_error);
                        tick5.setVisibility(View.VISIBLE);
                        progress5.setVisibility(View.GONE);
                        tvErrAtStep6.setVisibility(View.VISIBLE);
                        tvErrAtStep6.setText(R.string.error_prov_step_2);
                        hideLoading();
                    }
                });
            }

            @Override
            public void provisioningFailedFromDevice(final EfactorConstants.ProvisionFailureReason failureReason) {

                runOnUiThread(new Runnable() {

                    @Override
                    public void run() {

                        switch (failureReason) {

                            case AUTH_FAILED:
                                tvErrAtStep6.setText(R.string.error_authentication_failed);
                                displayFailureAtStep2();
                                break;

                            case NETWORK_NOT_FOUND:
                                tvErrAtStep6.setText(R.string.error_network_not_found);
                                displayFailureAtStep2();
                                break;

                            case DEVICE_DISCONNECTED:
                                doStep7(false);
                                break;

                            case UNKNOWN:
                                tvErrAtStep6.setText(R.string.error_prov_step_3);
                                displayFailureAtStep2();
                                break;
                        }
                    }
                });
            }

            @Override
            public void deviceProvisioningSuccess() {

                runOnUiThread(new Runnable() {

                    @Override
                    public void run() {
                        isProvisioningCompleted = true;
                        doStep7(true);
                    }
                });
            }

            @Override
            public void onProvisioningFailed(Exception e) {

                runOnUiThread(new Runnable() {

                    @Override
                    public void run() {

                        doStep7(false);
                    }
                });
            }
        });
    }

    private void displayFailureAtStep2() {

        tick6.setImageResource(R.drawable.ic_error);
        tick6.setVisibility(View.VISIBLE);
        progress6.setVisibility(View.GONE);
        tvErrAtStep6.setVisibility(View.VISIBLE);
        tvProvError.setVisibility(View.VISIBLE);
        hideLoading();
    }


    private void associateDevice() {

        Log.d(TAG, "Associate device");

        SharedPreferences sharedPreferences = getSharedPreferences(AppConstants.ESP_PREFERENCES, Context.MODE_PRIVATE);
        String userName = sharedPreferences.getString(AppConstants.KEY_USER_NAME, "");

        provisionManager.getEfactorDevice().sendDataToCustomEndPoint(userName, new ResponseListener() {

            @Override
            public void onSuccess(byte[] returnData) {


                runOnUiThread(new Runnable() {

                    @Override
                    public void run() {
                        doStep3();
                    }
                });
                processDetails(returnData);
            }

            @Override
            public void onFailure(Exception e) {

                Log.e(TAG, "Send config data : Error : " + e.getMessage());

                runOnUiThread(new Runnable() {

                    @Override
                    public void run() {
                        tick1.setImageResource(R.drawable.ic_error);
                        tick1.setVisibility(View.VISIBLE);
                        progress1.setVisibility(View.GONE);
                        tvErrAtStep1.setVisibility(View.VISIBLE);
                        tvErrAtStep1.setText("Failed to connect to device try again!");
                        hideLoading();
                    }
                });

                e.printStackTrace();
            }
        });
    }

    private void processDetails(byte[] responseData) {
        try {
            String decodedData = new String(responseData);

            String[] parts = decodedData.split("\\|");

            if (parts.length == 2) {
                String nodeId = parts[0];
                String secretKeyRes = parts[1];
                receivedNodeId = nodeId;
                secretKey = secretKeyRes;
                provision();
            } else {
                Log.e(TAG, "Invalid response format");
                runOnUiThread(new Runnable() {

                    @Override
                    public void run() {

                        tick2.setImageResource(R.drawable.ic_error);
                        tick2.setVisibility(View.VISIBLE);
                        progress2.setVisibility(View.GONE);
                        tvErrAtStep2.setVisibility(View.VISIBLE);
                        tvErrAtStep2.setText("user device configuration failed try again");
                        hideLoading();
                    }
                });
            }

        } catch (Exception e) {
            e.printStackTrace();
            runOnUiThread(new Runnable() {
                @Override
                public void run() {
                    tick2.setImageResource(R.drawable.ic_error);
                    tick2.setVisibility(View.VISIBLE);
                    progress2.setVisibility(View.GONE);
                    tvErrAtStep2.setVisibility(View.VISIBLE);
                    tvErrAtStep2.setText("User device configuration failed, try again");
                    hideLoading();
                }
            });
        }
    }


    private void addDeviceToCloud(final ApiLibResponseListener responseListener) {

        Log.d(TAG, "Add device to cloud, count : " + addDeviceReqCount);

        EfactorDeviceOperationRequest req=new EfactorDeviceOperationRequest();
        req.setDeviceId(receivedNodeId);
        req.setNodeId(receivedNodeId);
        req.setSecretKey(secretKey);
        apiManager.ADD(req, new ApiLibResponseListener() {
            @Override
            public void onSuccess(Bundle response) {
                responseListener.onSuccess(null);
            }

            @Override
            public void onResponseFailure(Bundle errorDetails) {
                String errorType = errorDetails.getString("errorType");
                String errorMessage = errorDetails.getString("errorMessage");
                Log.e(TAG, "Response Failure - Type: " + errorType + ", Message: " + errorMessage);
                responseListener.onResponseFailure(errorDetails);
            }

            @Override
            public void onNetworkFailure(Bundle errorDetails) {
                String errorType = errorDetails.getString("errorType");
                String errorMessage = errorDetails.getString("errorMessage");
                Log.e(TAG, "Response Failure - Type: " + errorType + ", Message: " + errorMessage);
                responseListener.onNetworkFailure(errorDetails);
            }
        });
    }


    private Runnable addDeviceTask = new Runnable() {

        @Override
        public void run() {

            addDeviceReqCount++;

            addDeviceToCloud(new ApiLibResponseListener() {

                @Override
                public void onSuccess(Bundle data) {

                    runOnUiThread(new Runnable() {

                        @Override
                        public void run() {
                            doStep8();
                        }
                    });
                }

                @Override
                public void onResponseFailure(Bundle errorDetails) {

                    if (addDeviceReqCount == 10) {

                        runOnUiThread(new Runnable() {

                            @Override
                            public void run() {

                                tick7.setImageResource(R.drawable.ic_error);
                                tick7.setVisibility(View.VISIBLE);
                                progress7.setVisibility(View.GONE);
                                tvErrAtStep7.setVisibility(View.VISIBLE);
                                tvErrAtStep7.setText(R.string.error_prov_step_3);
                                tvProvError.setVisibility(View.VISIBLE);
                                hideLoading();
                            }
                        });
                    } else {
                        handler.removeCallbacks(addDeviceTask);
                        handler.postDelayed(addDeviceTask, ADD_DEVICE_REQ_TIME);
                    }
                }

                @Override
                public void onNetworkFailure(Bundle errorDetails) {

                    if (addDeviceReqCount == 10) {

                        runOnUiThread(new Runnable() {

                            @Override
                            public void run() {

                                tick7.setImageResource(R.drawable.ic_error);
                                tick3.setVisibility(View.VISIBLE);
                                progress7.setVisibility(View.GONE);
                                tvErrAtStep7.setVisibility(View.VISIBLE);
                                tvErrAtStep7.setText(R.string.error_prov_step_3);
                                tvProvError.setVisibility(View.VISIBLE);
                                hideLoading();
                            }
                        });
                    } else {
                        handler.removeCallbacks(addDeviceTask);
                        handler.postDelayed(addDeviceTask, ADD_DEVICE_REQ_TIME);
                    }
                }
            });
        }
    };

    private Runnable getNodeStatusTask = new Runnable() {

        @Override
        public void run() {

            if (isFinishing()) {
                return;
            }

            EfactorDeviceOperationRequest req=new EfactorDeviceOperationRequest();
            req.setDeviceId(receivedNodeId);
            req.setNodeId(receivedNodeId);

            apiManager.GET_CONNECTIVITY_STATUS(req, new ApiLibResponseListener() {

                @Override
                public void onSuccess(Bundle data) {

                    runOnUiThread(new Runnable() {

                        @Override
                        public void run() {
                            boolean nodeStatus;
                            long timeStamp;
                            nodeStatus= data.getBoolean("nodeStatus",false);
                            timeStamp=data.getLong("timestamp",0);
                            if(nodeStatus)
                            {


                                if (timeStamp != 0) {
                                    Log.e(TAG, "Time zone service is available");
                                    TimeZone tz = TimeZone.getDefault();
                                    String timeZoneId = tz.getID();
                                    Log.e(TAG, "Time zone id : " + timeZoneId);

                                    JsonObject jsonParam = new JsonObject();
                                    jsonParam.addProperty(AppConstants.KEY_TIME, timeZoneId);

                                    EfactorDeviceOperationRequest req = new EfactorDeviceOperationRequest();
                                    req.setDeviceId(receivedNodeId);
                                    req.setNodeId(receivedNodeId);
                                    req.setTime_zone(timeZoneId);
  apiManager.SET_LOCATION_TIMEZONE(req, new ApiLibResponseListener() {
                                        @Override
                                        public void onSuccess(Bundle data) {
                                            runOnUiThread(new Runnable() {
                                                @Override
                                                public void run() {
                                                    hideLoading();
                                                    handler.removeCallbacks(nodeStatusReqFailed);
                                                    tick9.setImageResource(R.drawable.ic_checkbox_on);
                                                    tick9.setVisibility(View.VISIBLE);
                                                    progress9.setVisibility(View.GONE);
                                                    tvProvSuccess.setVisibility(View.VISIBLE);
                                                }
                                            });
                                        }

                                        @Override
                                        public void onResponseFailure(Bundle error) {
                                            runOnUiThread(new Runnable() {
                                                @Override
                                                public void run() {
                                                    Log.e(TAG, "Failed to send time zone value");
                                                    hideLoading();
                                                    handler.removeCallbacks(getNodeStatusTask);
                                                    tick9.setImageResource(R.drawable.ic_alert);
                                                    tick9.setVisibility(View.VISIBLE);
                                                    progress9.setVisibility(View.GONE);
                                                }
                                            });
                                        }

                                        @Override
                                        public void onNetworkFailure(Bundle error) {
                                            runOnUiThread(new Runnable() {
                                                @Override
                                                public void run() {
                                                    hideLoading();
                                                    Log.e(TAG, "Failed to send time zone value");
                                                    handler.removeCallbacks(getNodeStatusTask);
                                                    tick9.setImageResource(R.drawable.ic_alert);
                                                    tick9.setVisibility(View.VISIBLE);
                                                    progress9.setVisibility(View.GONE);
                                                }
                                            });
                                        }
                                    });
                                }
                                else {
                                    Log.e(TAG, "Time zone service is not available");
                                    tick9.setImageResource(R.drawable.ic_checkbox_on);
                                    tick9.setVisibility(View.VISIBLE);
                                    progress9.setVisibility(View.GONE);
                                    tvProvSuccess.setVisibility(View.VISIBLE);
                                    handler.removeCallbacks(nodeStatusReqFailed);
                                }
                            } else {
                                handler.removeCallbacks(getNodeStatusTask);
                                handler.postDelayed(getNodeStatusTask, 2000);
                            }
                        }
                    });
                }

                @Override
                public void onResponseFailure(Bundle error) {
                    handler.removeCallbacks(getNodeStatusTask);
                    handler.postDelayed(getNodeStatusTask, 2000);
                }

                @Override
                public void onNetworkFailure(Bundle error) {
                    handler.removeCallbacks(getNodeStatusTask);
                    handler.postDelayed(getNodeStatusTask, 2000);
                }
            });
        }
    };
    private Runnable nodeStatusReqFailed = new Runnable() {

        @Override
        public void run() {

            if (isFinishing()) {
                return;
            }
            Log.d(TAG, "Stop node status polling. Timeout");
            handler.removeCallbacks(getNodeStatusTask);
            tick9.setImageResource(R.drawable.ic_alert);
            tick9.setVisibility(View.VISIBLE);
            progress9.setVisibility(View.GONE);
            tvProvError.setVisibility(View.VISIBLE);
            hideLoading();
        }
    };

    private void showLoading() {

        btnOk.setEnabled(false);
        btnOk.setAlpha(0.5f);
    }

    public void hideLoading() {

        btnOk.setEnabled(true);
        btnOk.setAlpha(1f);
    }

    private void showAlertForDeviceDisconnected() {

        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setCancelable(false);
        builder.setTitle(R.string.error_title);
        builder.setMessage(R.string.dialog_msg_ble_device_disconnection);

        // Set up the buttons
        builder.setPositiveButton(R.string.btn_ok, new DialogInterface.OnClickListener() {

            @Override
            public void onClick(DialogInterface dialog, int which) {
                dialog.dismiss();
                setResult(RESULT_OK);
                finish();
            }
        });
        builder.show();
    }
}
