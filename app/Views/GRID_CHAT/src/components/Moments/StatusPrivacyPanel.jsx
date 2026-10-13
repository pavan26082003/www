import StatusAudiencePanel from "./StatusAudiencePanel";

const OPTIONS = [
  {
    value: "contacts",
    label: "My contacts",
    description: "Share with all of your contacts",
  },
  {
    value: "contacts_except",
    label: "My contacts except...",
    description: "Share with your contacts except people you select",
  },
  {
    value: "only",
    label: "Only share with...",
    description: "Select specific contacts",
  },
];

const PICKER_TITLE = {
  contacts_except: {
    title: "Hide status from...",
    subtitle: "These people will not see your new statuses",
  },
  only: {
    title: "Share status with...",
    subtitle: "Only these people will see your new statuses",
  },
};

function StatusPrivacyPanel(props) {
  return (
    <StatusAudiencePanel
      title="Status privacy"
      question="Who can see my statuses?"
      radioName="status-privacy"
      options={OPTIONS}
      pickerTitle={PICKER_TITLE}
      {...props}
    />
  );
}

export default StatusPrivacyPanel;
