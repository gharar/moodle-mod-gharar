export const init = (courseName) => {
    tieRoomNameToNameFieldIfUnmodified(courseName);
};

/**
 * Tie the room name, so it is automatically filled when the instance name is being updated.
 */
const tieRoomNameToNameFieldIfUnmodified = (courseName) => {
    let $inputName = document.getElementById("id_name"),
        $inputRoomName = document.getElementById("id_room_name");

    const generateStandardRoomName = () => {
        return courseName + " - " + $inputName.value;
    };
    const checkIsTied = () => {
        return generateStandardRoomName() === $inputRoomName.value;
    };

    let isTied = checkIsTied();

    $inputName.addEventListener("input", () => {
        if (isTied) {
            $inputRoomName.value = generateStandardRoomName();
        }
    });

    $inputRoomName.addEventListener("input", () => {
        isTied = checkIsTied();
    });
};
